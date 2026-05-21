<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre');
            $table->string('principio_activo')->nullable();
            $table->string('presentacion')->nullable();          // tableta, jarabe, ampolla...
            $table->string('concentracion')->nullable();         // 500mg, 250ml...
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->enum('tipo', ['generico', 'marca', 'controlado', 'refrigerado', 'cosmetico', 'insumo'])->default('generico');
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->integer('stock_maximo')->default(500);
            $table->string('ubicacion')->nullable();             // estante / pasillo
            $table->boolean('requiere_receta')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
