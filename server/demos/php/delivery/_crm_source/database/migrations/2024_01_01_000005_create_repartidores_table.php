<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repartidores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('dni', 15)->unique();
            $table->string('telefono', 20);
            $table->string('telefono_alt', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('foto')->nullable();
            $table->enum('tipo_vehiculo', ['moto', 'bicicleta', 'auto', 'pie'])->default('moto');
            $table->string('placa', 10)->nullable();
            $table->string('zona_asignada', 100)->nullable();
            $table->enum('estado', ['disponible', 'ocupado', 'inactivo', 'descanso'])->default('disponible');
            $table->decimal('calificacion', 3, 2)->default(5.00);
            $table->integer('total_entregas')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repartidores');
    }
};
