<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $files = $disk->files(config('backup.backup.name'));

        $backups = [];
        foreach ($files as $file) {
            if (substr($file, -4) == '.zip' && $disk->exists($file)) {
                $backups[] = [
                    'path' => $file,
                    'name' => str_replace(config('backup.backup.name') . '/', '', $file),
                    'size' => round($disk->size($file) / 1048576, 2) . ' MB',
                    'date' => \Carbon\Carbon::createFromTimestamp($disk->lastModified($file))->format('Y-m-d H:i:s')
                ];
            }
        }

        $backups = array_reverse($backups);

        return view('settings.backups', compact('backups'));
    }

    public function create()
    {
        try {
            // We dispatch the backup job to the background or run it synchronously
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();
            return redirect()->route('settings.backups.index')->with('success', 'Backup de base de datos generado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('settings.backups.index')->with('error', 'Error generando backup: ' . $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $file = $request->input('path');
        $diskName = config('backup.backup.destination.disks')[0];
        $disk = Storage::disk($diskName);

        if ($disk->exists($file)) {
            $fullPath = $disk->path($file);
            return response()->download($fullPath);
        }

        return redirect()->route('settings.backups.index')->with('error', 'El archivo no existe.');
    }
}
