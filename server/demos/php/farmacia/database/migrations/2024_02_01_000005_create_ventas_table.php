<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // cajero
            $table->enum('tipo_comprobante', ['boleta', 'factura', 'nota'])->default('boleta');
            $table->string('serie', 10)->nullable();
            $table->string('numero', 20)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('forma_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto', 'credito'])->default('efectivo');
            $table->decimal('pago_recibido', 12, 2)->default(0);
            $table->decimal('cambio', 12, 2)->default(0);
            $table->enum('estado', ['emitida', 'anulada', 'devuelta'])->default('emitida');
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });

        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
        Schema::dropIfExists('ventas');
    }
};
