<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convenios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('ruc', 20)->nullable();
            $table->string('tipo', 50)->default('Aseguradora'); // Aseguradora, Empresa, etc.
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->text('condiciones')->nullable();
            $table->string('contacto_nombre', 100)->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convenios');
    }
};
