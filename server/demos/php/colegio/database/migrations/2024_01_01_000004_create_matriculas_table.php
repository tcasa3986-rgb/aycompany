<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conceptos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 10, 2)->default(0);
            $table->enum('tipo', ['mensualidad', 'matricula', 'taller', 'otros'])->default('mensualidad');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->restrictOnDelete();
            $table->foreignId('seccion_id')->constrained('secciones')->restrictOnDelete();
            $table->year('anio_escolar');
            $table->date('fecha_matricula');
            $table->enum('estado', ['activo', 'retirado', 'trasladado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recibo', 30)->unique();
            $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete();
            $table->foreignId('concepto_id')->constrained('conceptos_pago')->restrictOnDelete();
            $table->year('anio_escolar');
            $table->tinyInteger('mes')->nullable()->comment('1-12 para mensualidades');
            $table->decimal('monto', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('monto_pagado', 10, 2);
            $table->date('fecha_pago');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'cheque'])->default('efectivo');
            $table->enum('estado', ['pagado', 'pendiente', 'vencido', 'anulado'])->default('pendiente');
            $table->string('comprobante', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('matriculas');
        Schema::dropIfExists('conceptos_pago');
    }
};
