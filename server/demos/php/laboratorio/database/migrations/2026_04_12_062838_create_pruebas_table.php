<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pruebas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas_laboratorio')->onDelete('restrict');
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('muestra_tipo', 80)->default('Sangre venosa');
            $table->integer('tiempo_resultado')->default(24)->comment('Horas estimadas');
            $table->decimal('precio', 10, 2)->default(0);
            $table->string('unidad', 30)->nullable();
            $table->string('valores_referencia', 200)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pruebas');
    }
};
