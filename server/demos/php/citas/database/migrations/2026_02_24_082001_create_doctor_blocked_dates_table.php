<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('blocked_date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'blocked_date']);
            $table->index(['doctor_id', 'blocked_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_blocked_dates');
    }
};
