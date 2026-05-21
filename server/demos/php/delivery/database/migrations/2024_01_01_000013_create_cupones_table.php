<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->enum('tipo', ['porcentaje', 'monto']);
            $table->decimal('valor', 8, 2);
            $table->decimal('monto_minimo', 8, 2)->default(0);
            $table->decimal('descuento_maximo', 8, 2)->nullable();
            $table->unsignedInteger('usos_maximos')->nullable();
            $table->unsignedInteger('usos_actuales')->default(0);
            $table->date('valido_desde')->nullable();
            $table->date('valido_hasta')->nullable();
            $table->boolean('solo_primer_pedido')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo','codigo']);
        });

        if (!Schema::hasColumn('pedidos', 'cupon_id')) {
            Schema::table('pedidos', function (Blueprint $t) {
                $t->foreignId('cupon_id')->nullable()->after('descuento')->constrained('cupones')->nullOnDelete();
                $t->string('codigo_cupon', 30)->nullable()->after('cupon_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'cupon_id')) {
            Schema::table('pedidos', function (Blueprint $t) {
                $t->dropForeign(['cupon_id']);
                $t->dropColumn(['cupon_id','codigo_cupon']);
            });
        }
        Schema::dropIfExists('cupones');
    }
};
