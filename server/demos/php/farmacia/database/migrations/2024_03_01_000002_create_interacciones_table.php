<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interacciones', function (Blueprint $table) {
            $table->id();
            $table->string('principio_a');           // ej. Paracetamol
            $table->string('principio_b');           // ej. Ibuprofeno
            $table->enum('severidad', ['baja', 'moderada', 'severa'])->default('moderada');
            $table->text('descripcion');
            $table->timestamps();

            $table->index(['principio_a', 'principio_b']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interacciones');
    }
};
