<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('historia_clinica', 20)->unique()->nullable();
            $table->string('tipo_documento', 20)->default('DNI');
            $table->string('numero_documento', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 80);
            $table->string('apellido_materno', 80)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('sexo', 1)->nullable(); // M/F
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('distrito', 80)->nullable();
            $table->string('ciudad', 80)->nullable();
            $table->string('tipo_sangre', 5)->nullable();
            $table->text('alergias')->nullable();
            $table->text('antecedentes')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
