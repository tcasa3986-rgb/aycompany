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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('insurance_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('insurance_coverage_amount', 8, 2)->default(0);
            $table->decimal('patient_copay_amount', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['insurance_id']);
            $table->dropColumn(['insurance_id', 'insurance_coverage_amount', 'patient_copay_amount']);
        });
    }
};
