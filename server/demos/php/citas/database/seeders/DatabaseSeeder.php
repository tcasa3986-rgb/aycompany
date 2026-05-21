<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Usuario admin para demo
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinica.com'],
            ['name' => 'Administrador', 'password' => 'password']
        );
        $admin->assignRole('admin');

        // Usuario recepcionista para demo
        $recep = User::firstOrCreate(
            ['email' => 'recepcion@clinica.com'],
            ['name' => 'Recepcionista', 'password' => 'password']
        );
        $recep->assignRole('receptionist');
    }
}
