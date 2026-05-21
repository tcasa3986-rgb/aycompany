<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('medico_id')->nullable()->constrained('medicos_referidores')->onDelete('set null');
            $table->dateTime('fecha_hora');
            $table->string('tipo_atencion', 80)->default('Consulta');
            $table->enum('estado', ['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No asistió'])->default('Programada');
            $table->text('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
