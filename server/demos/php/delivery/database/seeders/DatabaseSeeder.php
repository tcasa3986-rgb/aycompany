<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermisoSeeder::class,
            ConfiguracionSeeder::class,
            CategoriasSeeder::class,
            ProductosSeeder::class,
            ClientesSeeder::class,
            RepartidoresSeeder::class,
            ZonasSeeder::class,
            CuponesSeeder::class,
            PedidosSeeder::class,
        ]);
    }
}
