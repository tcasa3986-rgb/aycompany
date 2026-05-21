<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // farmaceutico que registra
            $table->string('medico');
            $table->string('especialidad')->nullable();
            $table->string('cmp', 20)->nullable();          // colegio médico
            $table->date('fecha');
            $table->boolean('retenida')->default(false);    // si la farmacia retiene la receta
            $table->text('diagnostico')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_receta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->string('indicaciones')->nullable();      // posología
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_receta');
        Schema::dropIfExists('recetas');
    }
};
