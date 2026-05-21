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
        // 1. Crear tabla sucursales
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Crear tabla pivote sucursal_user
        Schema::create('sucursal_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('es_predeterminada')->default(false);
            $table->timestamps();
        });

        // 3. Crear tabla sucursal_producto (Inventario)
        Schema::create('sucursal_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('ubicacion')->nullable();
            $table->timestamps();

            $table->unique(['sucursal_id', 'producto_id']);
        });

        // 4. Agregar sucursal_id a tablas transaccionales
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('user_id')->constrained('sucursales');
        });
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('user_id')->constrained('sucursales');
        });
        Schema::table('cajas', function (Blueprint $table) {
            $table->foreignId('sucursal_id')->nullable()->after('user_id')->constrained('sucursales');
        });

        // 5. Migración de Datos Existentes
        // Crear sucursal principal
        $sucursalId = DB::table('sucursales')->insertGetId([
            'nombre' => 'Casa Matriz',
            'direccion' => 'Sede Principal',
            'es_principal' => true,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mover stock de productos a sucursal_producto
        $productos = DB::table('productos')->get();
        foreach ($productos as $p) {
            DB::table('sucursal_producto')->insert([
                'sucursal_id' => $sucursalId,
                'producto_id' => $p->id,
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'ubicacion' => $p->ubicacion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Asignar todos los usuarios a la sucursal principal
        $users = DB::table('users')->get();
        foreach ($users as $u) {
            DB::table('sucursal_user')->insert([
                'sucursal_id' => $sucursalId,
                'user_id' => $u->id,
                'es_predeterminada' => true,
            ]);
        }

        // Actualizar sucursal_id en transacciones existentes
        DB::table('ventas')->update(['sucursal_id' => $sucursalId]);
        DB::table('compras')->update(['sucursal_id' => $sucursalId]);
        DB::table('cajas')->update(['sucursal_id' => $sucursalId]);

        // 6. Remover campos de productos (AHORA SON POR SUCURSAL)
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['stock', 'stock_minimo', 'ubicacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('ubicacion')->nullable();
        });

        Schema::table('cajas', function (Blueprint $table) { $table->dropConstrainedForeignId('sucursal_id'); });
        Schema::table('compras', function (Blueprint $table) { $table->dropConstrainedForeignId('sucursal_id'); });
        Schema::table('ventas', function (Blueprint $table) { $table->dropConstrainedForeignId('sucursal_id'); });

        Schema::dropIfExists('sucursal_producto');
        Schema::dropIfExists('sucursal_user');
        Schema::dropIfExists('sucursales');
    }
};
