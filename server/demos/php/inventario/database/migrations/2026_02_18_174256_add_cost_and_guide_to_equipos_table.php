<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->nullable()->after('fecha_adquisicion');
            $table->string('numero_guia')->nullable()->after('costo');
            $table->string('archivo_guia')->nullable()->after('numero_guia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn(['costo', 'numero_guia', 'archivo_guia']);
        });
    }
};
