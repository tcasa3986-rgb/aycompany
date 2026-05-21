<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            // A prescription might be directly from an appointment or medical record, or independent
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medication_name'); // e.g., Amoxicillin
            $table->string('dosage')->nullable(); // e.g., 500mg
            $table->string('frequency')->nullable(); // e.g., every 8 hours
            $table->string('duration')->nullable(); // e.g., for 7 days
            $table->text('instructions')->nullable(); // e.g., take with meals
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
