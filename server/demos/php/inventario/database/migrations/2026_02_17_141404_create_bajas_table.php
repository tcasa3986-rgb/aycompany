<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_equipo')->constrained('equipos')->cascadeOnUpdate();
            $table->date('fecha_baja');
            $table->string('motivo');
            $table->text('observaciones')->nullable();
            $table->string('acta_baja_path')->nullable();
            $table->text('descripcion_motivo')->nullable();
            $table->unsignedBigInteger('id_usuario_responsable')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bajas');
    }
};
