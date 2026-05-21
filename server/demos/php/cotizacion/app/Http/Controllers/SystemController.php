<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class SystemController extends Controller
{
    public function index()
    {
        return view('system.backup');
    }

    public function downloadBackup()
    {
        try {
            $pdo = DB::connection()->getPdo();
            $tables = array_map(function ($row) {
                $arr = (array) $row;
                return reset($arr);
            }, DB::select('SHOW TABLES'));

            $sql = "-- CotizaPro MySQL Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: " . DB::connection()->getDatabaseName() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // Tablas que NO deben incluirse en el respaldo:
            // - Sesiones/tokens: evitar deslogueos al restaurar
            // - Cache/jobs: contienen datos serializados con caracteres especiales
            //   que pueden romper el parsing SQL al restaurar
            $excludedTables = [
                'sessions',
                'migrations',
                'password_reset_tokens',
                'personal_access_tokens',
                'cache',
                'cache_locks',
                'jobs',
                'failed_jobs',
                'job_batches',
                'telescope_entries',
                'telescope_entries_tags',
                'telescope_monitoring',
            ];

            foreach ($tables as $table) {
                if (in_array($table, $excludedTables)) {
                    continue;
                }

                // Estructura de la tabla
                $createRow = DB::select("SHOW CREATE TABLE `$table`");
                if (!empty($createRow)) {
                    $createTable = $createRow[0]->{'Create Table'};
                    $sql .= "-- ------------------------------------------------\n";
                    $sql .= "-- Tabla: `$table`\n";
                    $sql .= "-- ------------------------------------------------\n";
                    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                    $sql .= $createTable . ";\n\n";
                }

                // Datos de la tabla
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `$table` VALUES \n";
                    $valuesArr = [];
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $escapedValues = array_map(function ($value) use ($pdo) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return $pdo->quote($value);
                        }, $rowArr);
                        $valuesArr[] = "(" . implode(', ', $escapedValues) . ")";
                    }
                    $sql .= implode(",\n", $valuesArr) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = 'cotizapro_backup_' . date('Y_m_d_H_i_s') . '.sql';

            return response($sql, 200, [
                'Content-Type'        => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control'       => 'no-cache, no-store, must-revalidate',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error generando el respaldo: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200', // max 50MB
        ]);

        $uploadedFile = $request->file('backup_file');

        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        if ($ext !== 'sql') {
            return back()->with('error', 'El archivo debe tener la extensión .sql (formato MySQL).');
        }

        try {
            $sqlContent = file_get_contents($uploadedFile->getRealPath());

            // Protección: eliminar cualquier bloque referente a la tabla 'sessions'
            // para evitar que el usuario sea deslogueado al restaurar.
            $protectedTables = ['sessions', 'cache', 'cache_locks', 'jobs', 'failed_jobs'];
            foreach ($protectedTables as $tbl) {
                $sqlContent = preg_replace('/DROP\s+TABLE\s+IF\s+EXISTS\s+`' . $tbl . '`\s*;/i', '', $sqlContent);
                $sqlContent = preg_replace('/CREATE\s+TABLE\s+`' . $tbl . '`[^;]*;/is', '', $sqlContent);
                $sqlContent = preg_replace('/INSERT\s+INTO\s+`' . $tbl . '`[^;]*;/is', '', $sqlContent);
            }

            // Usar PDO::exec() directamente: a diferencia de DB::unprepared(),
            // PDO con el driver MySQL sí soporta ejecutar múltiples sentencias
            // en un único string sin necesidad de hacer split manual por ';'
            // (lo que rompería si los valores de texto contienen ';').
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
            $pdo->exec($sqlContent);
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'Copia de seguridad MySQL restaurada correctamente.');

        } catch (\Exception $e) {
            try { DB::connection()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $ignore) {}
            return back()->with('error', 'Error al restaurar la copia de seguridad: ' . $e->getMessage());
        }
    }

    public function resetSystem(Request $request)
    {
        try {
            // Desactivar restricciones de claves foráneas para MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Vaciar tablas transaccionales y catálogos
            DB::table('quotation_details')->truncate();
            DB::table('quotations')->truncate();
            DB::table('products')->truncate();
            DB::table('clients')->truncate();
            DB::table('companies')->truncate();
            DB::table('settings')->truncate();

            // Volver a activar restricciones
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Re-poblar los settings por defecto
            $defaults = Setting::defaults();
            foreach ($defaults as $k => $v) {
                Setting::set($k, $v);
            }

            return back()->with('success', 'El sistema ha sido reseteado a sus valores de fábrica con éxito.');
        } catch (\Exception $e) {
            // Asegurar que las restricciones se reactivan en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Ocurrió un error al resetear el sistema: ' . $e->getMessage());
        }
    }
}
