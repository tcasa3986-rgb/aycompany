<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('restrict');
            $table->string('numero_factura', 30)->unique();
            $table->string('tipo_comprobante', 20)->default('Boleta');
            $table->foreignId('convenio_id')->nullable()->constrained('convenios')->onDelete('set null');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['Emitida', 'Pagada', 'Anulada'])->default('Emitida');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
