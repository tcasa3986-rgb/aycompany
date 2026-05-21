<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('limite_credito', 12, 2)->default(0)->after('puntos_fidelidad');
            $table->decimal('saldo_deudor', 12, 2)->default(0)->after('limite_credito');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['limite_credito', 'saldo_deudor']);
        });
    }
};
