<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@farmacia.test'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'phone'    => '999000000',
                'active'   => true,
            ]
        );
        $admin->syncRoles(['Administrador']);

        $cajero = User::updateOrCreate(
            ['email' => 'cajero@farmacia.test'],
            [
                'name'     => 'Cajero Demo',
                'password' => Hash::make('password'),
                'phone'    => '999111111',
                'active'   => true,
            ]
        );
        $cajero->syncRoles(['Cajero']);

        $farma = User::updateOrCreate(
            ['email' => 'farmaceutico@farmacia.test'],
            [
                'name'     => 'Farmaceutico Demo',
                'password' => Hash::make('password'),
                'phone'    => '999222222',
                'active'   => true,
            ]
        );
        $farma->syncRoles(['Farmaceutico']);
    }
}
