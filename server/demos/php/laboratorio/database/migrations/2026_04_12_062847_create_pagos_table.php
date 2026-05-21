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
            $table->foreignId('factura_id')->constrained()->onDelete('restrict');
            $table->decimal('monto', 10, 2);
            $table->string('medio_pago', 50)->default('Efectivo'); // Efectivo, Tarjeta, Transferencia, etc.
            $table->string('referencia', 100)->nullable();
            $table->dateTime('fecha_pago');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
