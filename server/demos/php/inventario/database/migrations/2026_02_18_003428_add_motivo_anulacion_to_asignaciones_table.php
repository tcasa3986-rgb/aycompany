<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->text('motivo_anulacion')->nullable()->after('observaciones_devolucion');
            // Modificar el enum para incluir 'Anulada'
            // En MySQL, DBAL no soporta modificar enums directamente de forma fácil con change()
            // Se recomienda usar raw SQL para modificar la columna
            DB::statement("ALTER TABLE asignaciones MODIFY COLUMN estado_asignacion ENUM('Activa', 'Finalizada', 'Anulada') NOT NULL DEFAULT 'Activa'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropColumn('motivo_anulacion');
            DB::statement("ALTER TABLE asignaciones MODIFY COLUMN estado_asignacion ENUM('Activa', 'Finalizada') NOT NULL DEFAULT 'Activa'");
        });
    }
};
