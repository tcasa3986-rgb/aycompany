<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'merma'])->default('entrada');
            $table->integer('cantidad'); // positivo o negativo
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->string('motivo', 200)->nullable();
            $table->timestamps();

            $table->index(['producto_id', 'created_at']);
            $table->index('tipo');
        });

        // Stock mínimo en productos
        if (!Schema::hasColumn('productos', 'stock_minimo')) {
            Schema::table('productos', function (Blueprint $t) {
                $t->unsignedInteger('stock_minimo')->default(5)->after('stock');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
        if (Schema::hasColumn('productos', 'stock_minimo')) {
            Schema::table('productos', function (Blueprint $t) {
                $t->dropColumn('stock_minimo');
            });
        }
    }
};
