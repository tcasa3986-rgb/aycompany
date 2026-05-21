<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_equipo')->constrained('equipos')->cascadeOnUpdate();
            $table->foreignId('id_empleado')->constrained('empleados')->cascadeOnUpdate();
            $table->dateTime('fecha_entrega');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->enum('estado_asignacion', ['Activa', 'Finalizada'])->default('Activa');
            $table->text('observaciones_entrega')->nullable();
            $table->text('observaciones_devolucion')->nullable();
            $table->string('acta_firmada_path')->nullable();
            $table->string('acta_devolucion_path')->nullable();
            $table->string('imagen_devolucion_1')->nullable();
            $table->string('imagen_devolucion_2')->nullable();
            $table->string('imagen_devolucion_3')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
