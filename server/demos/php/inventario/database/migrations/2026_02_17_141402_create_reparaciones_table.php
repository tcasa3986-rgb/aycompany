<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_equipo')->constrained('equipos');
            $table->date('fecha_ingreso');
            $table->date('fecha_salida')->nullable();
            $table->text('motivo');
            $table->string('proveedor_servicio')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->text('observaciones_salida')->nullable();
            $table->enum('estado_reparacion', ['En Proceso', 'Finalizada'])->default('En Proceso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};
