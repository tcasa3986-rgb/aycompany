<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('appointment_type_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('end_time')->nullable(); // Para saber cuándo termina exactamente la cita compuesta
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['appointment_type_id']);
            $table->dropColumn(['appointment_type_id', 'end_time']);
        });
    }
};
