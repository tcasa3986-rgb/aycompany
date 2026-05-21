<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100)->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->string('telefono', 20);
            $table->string('telefono_alt', 20)->nullable();
            $table->text('direccion');
            $table->string('referencia', 255)->nullable();
            $table->string('ciudad', 80)->nullable()->default('Lima');
            $table->string('distrito', 80)->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->enum('tipo', ['regular', 'frecuente', 'vip'])->default('regular');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
