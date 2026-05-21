<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'logo']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'general']
            );
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/img');
            $url = Storage::url($path);
            Setting::updateOrCreate(
                ['key' => 'company_logo'],
                ['value' => $url, 'group' => 'general', 'type' => 'image']
            );
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function backup()
    {
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $filename = "backup-" . now()->format('Y-m-d_H-i-s') . ".sql";
        $storagePath = storage_path("app/backups/");
        
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $fullPath = $storagePath . $filename;

        $command = "mysqldump --user={$dbUser} " . ($dbPass ? "--password={$dbPass} " : "") . "--host={$dbHost} {$dbName} > \"{$fullPath}\"";
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return back()->with('error', 'Error al generar el backup. Verifique que mysqldump esté instalado.');
        }

        return response()->download($fullPath)->deleteFileAfterSend(true);
    }
}
