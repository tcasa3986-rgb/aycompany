<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();

            // Clinical data
            $table->date('record_date');
            $table->text('chief_complaint');          // Motivo principal de consulta
            $table->text('diagnosis');                // Diagnóstico
            $table->text('treatment')->nullable();    // Tratamiento indicado
            $table->text('notes')->nullable();        // Notas clínicas adicionales
            $table->text('prescriptions')->nullable();// Recetas / medicamentos
            $table->text('referred_to')->nullable();  // Derivación a especialista

            // Vitals
            $table->string('blood_pressure', 20)->nullable();   // Presión arterial
            $table->string('heart_rate', 10)->nullable();        // Frecuencia cardíaca
            $table->string('temperature', 10)->nullable();       // Temperatura
            $table->string('weight', 10)->nullable();            // Peso (kg)
            $table->string('height', 10)->nullable();            // Talla (cm)
            $table->string('oxygen_saturation', 10)->nullable(); // SpO2

            $table->boolean('is_private')->default(false); // Visible sólo al médico
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
