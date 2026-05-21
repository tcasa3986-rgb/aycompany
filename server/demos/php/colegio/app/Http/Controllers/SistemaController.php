<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SistemaController extends Controller
{
    public function index()
    {
        return view('sistema.index');
    }

    public function backup()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        
        $filename = "backup_" . date("Y_m_d_H_i_s") . ".sql";
        $path = storage_path("app/backups");
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $filePath = $path . '/' . $filename;
        
        $passString = !empty($password) ? "-p{$password}" : "";
        $command = "mysqldump -u {$username} {$passString} {$database} > \"{$filePath}\"";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            return back()->with('error', 'Error al generar la copia de seguridad. Verifica que mysqldump esté disponible en tu sistema.');
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimetypes:text/plain,application/sql,text/x-sql|max:51200', // 50MB max
        ]);

        $file = $request->file('backup_file');
        
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        
        $path = $file->getRealPath();
        
        $passString = !empty($password) ? "-p{$password}" : "";
        $command = "mysql -u {$username} {$passString} {$database} < \"{$path}\"";
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            return back()->with('error', 'Error al restaurar la base de datos. El archivo podría estar corrupto.');
        }

        return back()->with('success', 'Base de datos restaurada correctamente.');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|in:RESETEAR'
        ]);

        try {
            // Eliminar todas las tablas y recrear estructura básica
            Artisan::call('migrate:fresh', ['--force' => true]);

            // Crear administrador por defecto
            User::create([
                'name'     => 'Administrador',
                'email'    => 'admin@colegio.edu.pe',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'activo'   => true,
            ]);

            Auth::logout();
            
            return redirect()->route('login')->with('status', 'El sistema ha sido reseteado a su estado de fábrica exitosamente. Inicia sesión nuevamente.');

        } catch (\Exception $e) {
            Log::error("Error al resetear el sistema: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error grave al resetear el sistema: ' . $e->getMessage());
        }
    }
}
