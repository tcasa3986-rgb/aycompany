<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('motivo_anulacion')->nullable()->after('observaciones');
            $table->timestamp('anulada_at')->nullable()->after('motivo_anulacion');
            $table->foreignId('anulada_por')->nullable()->after('anulada_at')
                  ->constrained('users')->nullOnDelete();
            $table->integer('puntos_canjeados')->default(0)->after('cambio');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anulada_por');
            $table->dropColumn(['motivo_anulacion', 'anulada_at', 'puntos_canjeados']);
        });
    }
};
