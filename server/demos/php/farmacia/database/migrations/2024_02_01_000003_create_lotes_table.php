<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('numero_lote', 80);
            $table->date('fecha_vencimiento');
            $table->integer('cantidad')->default(0);
            $table->timestamps();

            $table->index(['producto_id', 'fecha_vencimiento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
