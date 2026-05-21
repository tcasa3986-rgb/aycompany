<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 20)->unique();
            $table->foreignId('paciente_id')->constrained()->onDelete('restrict');
            $table->foreignId('medico_id')->nullable()->constrained('medicos_referidores')->onDelete('set null');
            $table->foreignId('convenio_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('restrict')->comment('Recepcionista que registró');
            $table->dateTime('fecha_registro');
            $table->string('diagnostico_presuntivo', 250)->nullable();
            $table->enum('estado', ['Pendiente', 'En proceso', 'Completado', 'Entregado', 'Anulado'])->default('Pendiente');
            $table->enum('prioridad', ['Normal', 'Urgente', 'Emergencia'])->default('Normal');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('pagado')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
