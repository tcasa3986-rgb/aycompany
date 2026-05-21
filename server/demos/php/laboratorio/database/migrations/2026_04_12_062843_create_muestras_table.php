<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muestras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->string('codigo_muestra', 30)->unique();
            $table->string('tipo_muestra', 80);
            $table->dateTime('fecha_toma');
            $table->foreignId('tomado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('estado', ['Recibida', 'En análisis', 'Analizada', 'Rechazada'])->default('Recibida');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muestras');
    }
};
