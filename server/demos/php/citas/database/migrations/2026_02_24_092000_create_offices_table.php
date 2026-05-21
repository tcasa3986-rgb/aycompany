<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('name');                            // Ej: "Consultorio 1", "Clínica Norte"
            $table->string('address')->nullable();             // Dirección del consultorio
            $table->string('floor')->nullable();               // Piso / edificio
            $table->string('phone')->nullable();               // Teléfono directo
            $table->string('maps_url')->nullable();            // Enlace a Google Maps
            $table->boolean('is_active')->default(true);       // Activo / inactivo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
