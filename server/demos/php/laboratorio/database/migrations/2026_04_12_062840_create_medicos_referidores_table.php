<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos_referidores', function (Blueprint $table) {
            $table->id();
            $table->string('cmp', 20)->unique()->nullable();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('especialidad', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('institucion', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos_referidores');
    }
};
