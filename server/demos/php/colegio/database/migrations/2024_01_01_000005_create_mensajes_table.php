<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('destinatario_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto', 200);
            $table->text('cuerpo');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_en')->nullable();
            $table->boolean('archivado')->default(false);
            $table->timestamps();
        });

        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->enum('tipo', ['info', 'exito', 'advertencia', 'error'])->default('info');
            $table->boolean('leido')->default(false);
            $table->string('url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('mensajes');
    }
};
