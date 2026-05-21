<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dni', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->enum('tipo', ['docente', 'administrativo', 'directivo', 'auxiliar'])->default('docente');
            $table->string('especialidad', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_ingreso');
            $table->decimal('salario', 10, 2)->default(0);
            $table->enum('estado', ['activo', 'inactivo', 'licencia'])->default('activo');
            $table->string('foto', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
