<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL is often more reliable for modifying ENUM columns in MySQL/MariaDB
        // to avoid doctrine/dbal dependencies or issues.
        DB::statement("ALTER TABLE reparaciones MODIFY COLUMN estado_reparacion ENUM('Pendiente', 'En Proceso', 'Completada', 'Cancelada') NOT NULL DEFAULT 'Pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to the original state (though warning: this might truncate data if 'Pendiente' or 'Cancelada' exist)
        // Original definition was ['En Proceso', 'Finalizada'] default 'En Proceso'

        // We first map 'Pendiente' -> 'En Proceso' and 'Completada'/'Cancelada' -> 'Finalizada' to avoid data loss on rollback if possible
        DB::statement("UPDATE reparaciones SET estado_reparacion = 'En Proceso' WHERE estado_reparacion = 'Pendiente'");
        DB::statement("UPDATE reparaciones SET estado_reparacion = 'Finalizada' WHERE estado_reparacion IN ('Completada', 'Cancelada')");

        DB::statement("ALTER TABLE reparaciones MODIFY COLUMN estado_reparacion ENUM('En Proceso', 'Finalizada') NOT NULL DEFAULT 'En Proceso'");
    }
};
