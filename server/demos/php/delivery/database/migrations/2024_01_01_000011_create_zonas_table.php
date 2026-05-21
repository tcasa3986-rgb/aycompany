<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('distrito', 100)->nullable();
            $table->decimal('costo_delivery', 8, 2)->default(0);
            $table->unsignedSmallInteger('tiempo_estimado_min')->default(30);
            $table->decimal('monto_minimo_pedido', 8, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'distrito']);
        });

        // Añadir relación opcional desde pedidos
        if (Schema::hasTable('pedidos') && !Schema::hasColumn('pedidos', 'zona_id')) {
            Schema::table('pedidos', function (Blueprint $t) {
                $t->foreignId('zona_id')->nullable()->after('distrito_entrega')->constrained('zonas')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'zona_id')) {
            Schema::table('pedidos', function (Blueprint $t) {
                $t->dropForeign(['zona_id']);
                $t->dropColumn('zona_id');
            });
        }
        Schema::dropIfExists('zonas');
    }
};
