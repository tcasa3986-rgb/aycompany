<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('registrado_por')->constrained('users')->onDelete('restrict');
            $table->string('referencia', 60)->nullable();
            $table->enum('metodo', ['efectivo', 'tarjeta', 'transferencia', 'yape', 'plin', 'otro'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->decimal('vuelto', 10, 2)->default(0);
            $table->enum('estado', ['completado', 'pendiente', 'rechazado', 'reembolsado'])->default('completado');
            $table->string('comprobante_tipo', 20)->nullable(); // boleta, factura, ninguno
            $table->string('comprobante_numero', 30)->nullable();
            $table->text('notas')->nullable();
            $table->timestamp('fecha_pago')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
