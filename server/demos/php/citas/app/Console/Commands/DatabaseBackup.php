<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un backup completo de la base de datos MySQL y elimina los de más de 30 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $storagePath = storage_path('app/backups');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $dsn = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');

        // Construir comando (mysqldump). Password se inyecta directamente.
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            escapeshellarg($user),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($dsn),
            escapeshellarg($storagePath . DIRECTORY_SEPARATOR . $filename)
        );

        $this->info("Iniciando respaldo de base de datos...");

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("¡Respaldo efectuado exitosamente! Archivo creado: {$filename}");
        } else {
            $this->error("Hubo un fallo generando el respaldo. (Código de error: {$returnVar})");
            return Command::FAILURE;
        }

        // Limpieza de backups que tengan más de 30 días.
        $this->info("Iniciando purga de backups antiguos...");
        $files = \File::files($storagePath);
        $deletedCount = 0;

        foreach ($files as $file) {
            if (now()->diffInDays(\Carbon\Carbon::createFromTimestamp(\File::lastModified($file))) > 30) {
                \File::delete($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Se eliminaron {$deletedCount} backups antiguos para ahorrar espacio.");
        }

        return Command::SUCCESS;
    }
}
