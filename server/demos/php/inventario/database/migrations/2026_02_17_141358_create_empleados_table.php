<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sucursal')->constrained('sucursales');
            $table->string('dni', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->foreignId('id_cargo')->nullable()->constrained('cargos')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_area')->nullable()->constrained('areas')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
