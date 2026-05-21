<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('repartidores', 'lat_actual')) {
            Schema::table('repartidores', function (Blueprint $t) {
                $t->decimal('lat_actual', 10, 7)->nullable()->after('estado');
                $t->decimal('lng_actual', 10, 7)->nullable()->after('lat_actual');
                $t->timestamp('ultima_ubicacion_at')->nullable()->after('lng_actual');
            });
        }
        if (!Schema::hasColumn('entregas', 'lat_entrega')) {
            Schema::table('entregas', function (Blueprint $t) {
                $t->decimal('lat_entrega', 10, 7)->nullable();
                $t->decimal('lng_entrega', 10, 7)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('repartidores', 'lat_actual')) {
            Schema::table('repartidores', function (Blueprint $t) {
                $t->dropColumn(['lat_actual','lng_actual','ultima_ubicacion_at']);
            });
        }
        if (Schema::hasColumn('entregas', 'lat_entrega')) {
            Schema::table('entregas', function (Blueprint $t) {
                $t->dropColumn(['lat_entrega','lng_entrega']);
            });
        }
    }
};
