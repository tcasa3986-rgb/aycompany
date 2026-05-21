<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Settings table ────────────────────────────────
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // ── products: add unit ────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 30)->nullable()->after('price')
                  ->comment('Unidad de medida: und, kg, hr, m2, lt...');
        });

        // ── quotations: add currency, discount, terms ─────
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('currency', 3)->default('PEN')->after('client_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
            $table->text('terms')->nullable()->after('notes');
        });

        // ── quotation_details: add discount_pct, unit ─────
        Schema::table('quotation_details', function (Blueprint $table) {
            $table->decimal('discount_pct', 5, 2)->default(0)->after('unit_price');
            $table->string('unit', 30)->nullable()->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::table('products', fn($t) => $t->dropColumn('unit'));
        Schema::table('quotations', fn($t) => $t->dropColumn(['currency', 'discount_amount', 'terms']));
        Schema::table('quotation_details', fn($t) => $t->dropColumn(['discount_pct', 'unit']));
    }
};
