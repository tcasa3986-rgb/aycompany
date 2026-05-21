<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->enum('nivel', ['inicial', 'primaria', 'secundaria'])->default('primaria');
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->string('nombre', 10);
            $table->enum('turno', ['mañana', 'tarde', 'noche'])->default('mañana');
            $table->integer('capacidad')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secciones');
        Schema::dropIfExists('grados');
    }
};
