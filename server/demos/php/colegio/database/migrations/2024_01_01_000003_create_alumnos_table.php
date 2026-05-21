<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('dni', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F']);
            $table->text('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('foto', 255)->nullable();
            // Apoderado
            $table->string('apoderado_nombre', 150)->nullable();
            $table->string('apoderado_dni', 20)->nullable();
            $table->string('apoderado_telefono', 20)->nullable();
            $table->string('apoderado_email', 150)->nullable();
            $table->string('apoderado_parentesco', 50)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'trasladado', 'egresado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
