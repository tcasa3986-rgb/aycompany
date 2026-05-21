<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas_laboratorio')->onDelete('restrict');
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 150);
            $table->string('marca', 100)->nullable();
            $table->string('proveedor', 150)->nullable();
            $table->string('unidad_medida', 30)->default('Unidad');
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('lote', 50)->nullable();
            $table->enum('estado', ['Disponible', 'Stock bajo', 'Sin stock', 'Vencido'])->default('Disponible');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactivos');
    }
};
