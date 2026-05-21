<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('repartidor_id')->constrained('repartidores')->onDelete('restrict');
            $table->foreignId('asignado_por')->constrained('users')->onDelete('restrict');
            $table->enum('estado', ['asignado', 'recogido', 'en_camino', 'entregado', 'fallido', 'devuelto'])->default('asignado');
            $table->datetime('fecha_asignacion');
            $table->datetime('fecha_recogida')->nullable();
            $table->datetime('fecha_entrega_estimada')->nullable();
            $table->datetime('fecha_entrega_real')->nullable();
            $table->decimal('distancia_km', 8, 2)->nullable();
            $table->integer('tiempo_minutos')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('firma_cliente')->nullable();
            $table->string('foto_evidencia')->nullable();
            $table->decimal('calificacion', 3, 2)->nullable();
            $table->text('comentario_cliente')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
