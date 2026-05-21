<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearOperationalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate tables in logical order (though with FK disabled it matters less)
        DB::table('bajas')->truncate();
        DB::table('reparaciones')->truncate();
        DB::table('asignaciones')->truncate();
        DB::table('equipos')->truncate();
        DB::table('empleados')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('Operational data (Bajas, Reparaciones, Asignaciones, Equipos, Empleados) cleared successfully!');
    }
}
