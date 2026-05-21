<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // operador
            $table->foreignId('repartidor_id')->nullable()->constrained('repartidores')->onDelete('set null');
            $table->text('direccion_entrega');
            $table->string('referencia_entrega', 255)->nullable();
            $table->string('distrito_entrega', 80)->nullable();
            $table->enum('estado', [
                'pendiente',
                'confirmado',
                'preparando',
                'listo',
                'en_camino',
                'entregado',
                'cancelado',
                'devuelto'
            ])->default('pendiente');
            $table->enum('tipo_pago', ['efectivo', 'tarjeta', 'transferencia', 'yape', 'plin'])->default('efectivo');
            $table->enum('estado_pago', ['pendiente', 'pagado', 'parcial'])->default('pendiente');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('costo_delivery', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->datetime('fecha_programada')->nullable();
            $table->datetime('fecha_entrega')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
