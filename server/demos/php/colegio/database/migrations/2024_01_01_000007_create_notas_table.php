<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->year('anio_escolar');
            $table->tinyInteger('bimestre'); // 1, 2, 3, 4
            $table->decimal('nota', 5, 2)->nullable();   // 0.00 – 20.00
            $table->decimal('promedio_bimestral', 5, 2)->nullable();
            $table->enum('estado', ['aprobado', 'desaprobado', 'pendiente'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['alumno_id', 'materia_id', 'seccion_id', 'anio_escolar', 'bimestre'], 'nota_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
