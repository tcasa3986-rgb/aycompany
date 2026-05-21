<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('primary_doctor_id')
                ->nullable()
                ->after('address')
                ->constrained('doctors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Doctor::class, 'primary_doctor_id');
            $table->dropColumn('primary_doctor_id');
        });
    }
};
