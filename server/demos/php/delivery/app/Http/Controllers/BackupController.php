<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    private string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function index()
    {
        $files = collect(File::files($this->backupPath))
            ->map(fn($f) => [
                'nombre'    => $f->getFilename(),
                'tamaño'    => $this->formatBytes($f->getSize()),
                'fecha'     => Carbon::createFromTimestamp($f->getMTime()),
                'ruta'      => $f->getPathname(),
            ])
            ->sortByDesc('fecha')
            ->values();

        // Estadísticas del sistema
        $stats = [
            'clientes'     => DB::table('clientes')->count(),
            'productos'    => DB::table('productos')->count(),
            'pedidos'      => DB::table('pedidos')->count(),
            'repartidores' => DB::table('repartidores')->count(),
            'usuarios'     => DB::table('users')->count(),
            'tamano_bd'    => $this->getDatabaseSize(),
        ];

        return view('backups.index', compact('files', 'stats'));
    }

    /**
     * Genera un dump SQL completo de la BD
     */
    public function crear(Request $request)
    {
        $request->validate([
            'descripcion' => 'nullable|string|max:120',
        ]);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "backup_{$timestamp}.sql";
        $filepath  = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        try {
            $sql  = "-- Backup CRM Delivery\n";
            $sql .= "-- Fecha: " . now()->format('Y-m-d H:i:s') . "\n";
            $sql .= "-- Descripción: " . ($request->descripcion ?: 'Backup manual') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = $this->getAllTables();

            foreach ($tables as $table) {
                $sql .= $this->dumpTable($table);
            }

            $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filepath, $sql);

            return back()->with('success', "Backup creado: {$filename} (" . $this->formatBytes(filesize($filepath)) . ")");
        } catch (\Throwable $e) {
            if (File::exists($filepath)) File::delete($filepath);
            return back()->with('error', 'Error al generar backup: ' . $e->getMessage());
        }
    }

    public function descargar(string $archivo)
    {
        $archivo = basename($archivo); // sanitizar
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $archivo;
        abort_unless(File::exists($filepath), 404);
        return response()->download($filepath);
    }

    public function eliminar(string $archivo)
    {
        $archivo = basename($archivo);
        $filepath = $this->backupPath . DIRECTORY_SEPARATOR . $archivo;
        if (File::exists($filepath)) {
            File::delete($filepath);
            return back()->with('success', 'Backup eliminado.');
        }
        return back()->with('error', 'Backup no encontrado.');
    }

    /**
     * Restaura desde archivo subido o backup existente
     */
    public function restaurar(Request $request)
    {
        $request->validate([
            'archivo'        => 'nullable|file|mimes:sql,txt|max:51200',
            'archivo_local'  => 'nullable|string',
            'confirmacion'   => 'required|in:RESTAURAR',
        ]);

        try {
            // Obtener contenido SQL
            if ($request->hasFile('archivo')) {
                $sql = File::get($request->file('archivo')->getRealPath());
            } elseif ($request->filled('archivo_local')) {
                $local = $this->backupPath . DIRECTORY_SEPARATOR . basename($request->archivo_local);
                abort_unless(File::exists($local), 404);
                $sql = File::get($local);
            } else {
                return back()->with('error', 'Debes seleccionar un archivo o un backup existente.');
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Ejecutar las sentencias separadas por ;
            // Truco: dividir por ";\n" para evitar problemas con datos que contengan ";"
            $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
            foreach ($statements as $stmt) {
                if (empty($stmt) || str_starts_with($stmt, '--')) continue;
                DB::unprepared($stmt . ';');
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            Artisan::call('cache:clear');

            return back()->with('success', 'Backup restaurado correctamente. Cierra sesión y vuelve a entrar.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    /**
     * Reset del sistema con 3 niveles distintos
     */
    public function reset(Request $request)
    {
        $request->validate([
            'nivel'        => 'required|in:transaccional,operativo,total',
            'confirmacion' => 'required|in:RESET',
        ]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Tablas transaccionales (siempre se borran)
            $transaccionales = ['pagos', 'entregas', 'pedido_items', 'pedidos', 'movimientos_stock'];
            foreach ($transaccionales as $t) {
                if (Schema::hasTable($t)) DB::table($t)->truncate();
            }

            if (in_array($request->nivel, ['operativo','total'])) {
                // Operativo: además limpiar clientes, repartidores no usuarios, productos
                $operativos = ['clientes', 'repartidores', 'productos', 'cupones'];
                foreach ($operativos as $t) {
                    if (Schema::hasTable($t)) DB::table($t)->truncate();
                }
            }

            if ($request->nivel === 'total') {
                // Total: además zonas, categorías, configuración
                $totales = ['zonas', 'categorias', 'configuraciones'];
                foreach ($totales as $t) {
                    if (Schema::hasTable($t)) DB::table($t)->truncate();
                }
                // Re-sembrar configuración base
                Artisan::call('db:seed', ['--class' => 'ConfiguracionSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'CategoriasSeeder', '--force' => true]);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            Artisan::call('cache:clear');

            $msg = match ($request->nivel) {
                'transaccional' => 'Datos transaccionales limpiados (pedidos, pagos, entregas, movimientos de stock).',
                'operativo'     => 'Sistema reseteado a nivel operativo. Clientes, productos, repartidores y cupones eliminados. Configuración y usuarios intactos.',
                'total'         => 'Reset total ejecutado. Sistema listo para una nueva empresa. Solo usuarios y roles permanecen.',
            };

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al resetear: ' . $e->getMessage());
        }
    }

    // ==================== Helpers ====================

    private function getAllTables(): array
    {
        $rows = DB::select('SHOW TABLES');
        $tables = [];
        foreach ($rows as $row) {
            $tables[] = array_values((array) $row)[0];
        }
        return $tables;
    }

    private function dumpTable(string $table): string
    {
        $sql = "\n-- ----------------------------------\n";
        $sql .= "-- Tabla: {$table}\n";
        $sql .= "-- ----------------------------------\n";

        // Estructura
        $create = DB::select("SHOW CREATE TABLE `{$table}`");
        if (!empty($create)) {
            $createSql = (array) $create[0];
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= ($createSql['Create Table'] ?? $createSql['Create View'] ?? '') . ";\n\n";
        }

        // Datos
        $rows = DB::table($table)->get();
        if ($rows->count() > 0) {
            $columns = array_keys((array) $rows->first());
            $cols = '`' . implode('`,`', $columns) . '`';

            $batches = $rows->chunk(100);
            foreach ($batches as $batch) {
                $values = [];
                foreach ($batch as $row) {
                    $vals = [];
                    foreach ((array) $row as $v) {
                        if (is_null($v))      $vals[] = 'NULL';
                        elseif (is_bool($v))  $vals[] = $v ? '1' : '0';
                        elseif (is_numeric($v)) $vals[] = $v;
                        else                  $vals[] = "'" . str_replace(["\\","'","\n","\r"], ["\\\\","\\'","\\n","\\r"], (string) $v) . "'";
                    }
                    $values[] = '(' . implode(',', $vals) . ')';
                }
                $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES\n" . implode(",\n", $values) . ";\n";
            }
            $sql .= "\n";
        }
        return $sql;
    }

    private function getDatabaseSize(): string
    {
        $db = config('database.connections.mysql.database');
        $row = DB::select("SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = ?", [$db]);
        $bytes = $row[0]->size ?? 0;
        return $this->formatBytes((int) $bytes);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B','KB','MB','GB','TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
