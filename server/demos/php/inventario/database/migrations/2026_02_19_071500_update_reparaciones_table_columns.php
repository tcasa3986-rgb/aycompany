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
        Schema::table('reparaciones', function (Blueprint $table) {
            // Rename columns to match code
            $table->renameColumn('motivo', 'descripcion_problema');
            $table->renameColumn('costo', 'costo_real');

            // Add missing columns
            $table->string('tecnico_asignado', 100)->nullable()->after('fecha_ingreso');
            $table->decimal('costo_estimado', 10, 2)->nullable()->after('proveedor_servicio'); // After proveedor_servicio or similar
            $table->text('descripcion_solucion')->nullable()->after('descripcion_problema');

            // Make existing columns nullable if they aren't used in Create
            $table->string('proveedor_servicio')->nullable()->change();
            $table->text('observaciones_salida')->nullable()->change();

            // Allow costo_real to be nullable (as it is 0 by default, but let's make it explicit nullable if needed, 
            // though keeping default 0 is fine if we just rename it. 
            // Ideally receiving null from form should reach here as null if we want strictness, 
            // but decimal usually has default. Let's make it nullable to match validation 'nullable|numeric'.)
            $table->decimal('costo_real', 10, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->renameColumn('descripcion_problema', 'motivo');
            $table->renameColumn('costo_real', 'costo');

            $table->dropColumn(['tecnico_asignado', 'costo_estimado', 'descripcion_solucion']);

            $table->decimal('costo', 10, 2)->default(0)->change();
        });
    }
};
