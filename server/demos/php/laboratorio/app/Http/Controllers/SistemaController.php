<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Ifsnop\Mysqldump as IMysqldump;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class SistemaController extends Controller
{
    public function mantenimiento()
    {
        return view('sistema.mantenimiento');
    }

    public function generarManualPdf()
    {
        try {
            $pdf = Pdf::loadView('reports.manual_tecnico');
            return $pdf->download('Manual_Tecnico_LabSalud.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el manual: ' . $e->getMessage());
        }
    }

    public function backup()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password') ?? '';
            $dbHost = config('database.connections.mysql.host') ?? '127.0.0.1';
            
            // Build DSN
            $dsn = "mysql:host={$dbHost};dbname={$dbName}";
            
            // Configuration options
            $dumpSettings = array(
                'add-drop-table' => true,
                'add-locks' => true,
                'extended-insert' => true,
                'disable-keys' => true
            );
            
            // Generate dump
            $dump = new IMysqldump\Mysqldump($dsn, $dbUser, $dbPass, $dumpSettings);
            
            $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = storage_path('app/' . $fileName);
            
            $dump->start($filePath);
            
            // Download and delete the temp file
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar la copia de seguridad: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        try {
            $file = $request->file('backup_file');
            $extension = $file->getClientOriginalExtension();
            
            if (strtolower($extension) !== 'sql') {
                return back()->with('error', 'El archivo debe tener extensión .sql');
            }

            $sqlPath = $file->getRealPath();
            $sql = File::get($sqlPath);
            
            // Execute raw SQL file content for restoring
            DB::unprepared($sql);

            return back()->with('success', 'Base de datos restaurada correctamente. Todas las tablas han sido sobreescritas de la copia.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar la base de datos: ' . $e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        try {
            $tablesToTruncate = [];

            // Transacciones
            if ($request->has('clear_transactions')) {
                $tablesToTruncate = array_merge($tablesToTruncate, [
                    'pagos', 'facturas', 'resultados', 'muestras', 'orden_detalles', 'ordenes'
                ]);
            }

            // Pacientes
            if ($request->has('clear_patients')) {
                $tablesToTruncate = array_merge($tablesToTruncate, ['pacientes']);
            }

            // Recursos Médicos
            if ($request->has('clear_resources')) {
                $tablesToTruncate = array_merge($tablesToTruncate, ['medicos_referidores', 'convenios']);
            }

            // Catálogo (Inventario, Pruebas y Áreas)
            if ($request->has('clear_catalog')) {
                $tablesToTruncate = array_merge($tablesToTruncate, [
                    'reactivos', 'pruebas', 'areas_laboratorio'
                ]);
            }

            if (count($tablesToTruncate) > 0) {
                // Disable foreign keys temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                foreach ($tablesToTruncate as $table) {
                    DB::table($table)->truncate();
                }
                
                // Re-enable foreign keys
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                return back()->with('success', 'El sistema ha sido reseteado para una empresa nueva exitosamente.');
            }

            return back()->with('info', 'No se seleccionó ninguna opción para restablecer el sistema.');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Error al resetear el sistema: ' . $e->getMessage());
        }
    }
}
