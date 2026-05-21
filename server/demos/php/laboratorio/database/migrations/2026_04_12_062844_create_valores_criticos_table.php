<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valores_criticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_id')->constrained('resultados')->onDelete('cascade');
            $table->unsignedBigInteger('orden_id');
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->string('descripcion', 250);
            $table->string('notificado_a', 150)->nullable();
            $table->dateTime('fecha_notificacion')->nullable();
            $table->enum('estado', ['Pendiente', 'Notificado', 'Resuelto'])->default('Pendiente');
            $table->text('accion_tomada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valores_criticos');
    }
};
