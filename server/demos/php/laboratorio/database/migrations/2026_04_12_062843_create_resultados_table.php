<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_detalle_id');
            $table->foreign('orden_detalle_id')->references('id')->on('orden_detalles')->onDelete('cascade');
            $table->foreignId('muestra_id')->nullable()->constrained('muestras')->onDelete('set null');
            $table->text('valor')->nullable();
            $table->string('unidad', 30)->nullable();
            $table->string('valores_referencia', 200)->nullable();
            $table->enum('interpretacion', ['Normal', 'Bajo', 'Alto', 'Crítico'])->nullable();
            $table->string('metodo', 100)->nullable();
            $table->string('equipo', 100)->nullable();
            $table->foreignId('validado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_validacion')->nullable();
            $table->boolean('valor_critico')->default(false);
            $table->boolean('notificado')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
