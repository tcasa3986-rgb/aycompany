<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sucursal')->constrained('sucursales');
            $table->string('codigo_inventario', 50)->unique();
            $table->foreignId('id_tipo_equipo')->constrained('tipos_equipo');
            $table->foreignId('id_marca')->constrained('marcas');
            $table->foreignId('id_modelo')->constrained('modelos');
            $table->string('numero_serie', 100)->unique();
            $table->text('caracteristicas')->nullable();
            $table->enum('tipo_adquisicion', ['Propio', 'Arrendado', 'Prestamo']);
            $table->date('fecha_adquisicion')->nullable();
            $table->string('proveedor', 150)->nullable();
            $table->enum('estado', ['Disponible', 'Asignado', 'En Reparacion', 'De Baja'])->default('Disponible');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
