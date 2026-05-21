<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Materias / Cursos
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->unique();
            $table->enum('nivel', ['inicial', 'primaria', 'secundaria', 'todos'])->default('todos');
            $table->integer('horas_semanales')->default(2);
            $table->string('color', 20)->default('#3b82f6'); // para el UI
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Asignaciones: docente → materia → sección → año
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('personal')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->year('anio_escolar');
            $table->timestamps();
            $table->unique(['personal_id', 'materia_id', 'seccion_id', 'anio_escolar'], 'asignacion_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
        Schema::dropIfExists('materias');
    }
};
