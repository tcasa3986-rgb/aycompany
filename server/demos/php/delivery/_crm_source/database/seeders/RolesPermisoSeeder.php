<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesPermisoSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos del sistema
        $permisos = [
            // Dashboard
            'ver dashboard',
            // Clientes
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            // Productos
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            // Pedidos
            'ver pedidos', 'crear pedidos', 'editar pedidos', 'cancelar pedidos',
            // Repartidores
            'ver repartidores', 'crear repartidores', 'editar repartidores', 'eliminar repartidores',
            // Entregas
            'ver entregas', 'asignar entregas', 'actualizar entregas',
            // Pagos
            'ver pagos', 'registrar pagos',
            // Reportes
            'ver reportes',
            // Usuarios
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            // Configuracion
            'ver configuracion', 'editar configuracion',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $operador   = Role::firstOrCreate(['name' => 'operador']);
        $repartidor = Role::firstOrCreate(['name' => 'repartidor']);

        // Super Admin: todos los permisos
        $superAdmin->syncPermissions(Permission::all());

        // Admin
        $admin->syncPermissions([
            'ver dashboard',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver productos', 'crear productos', 'editar productos',
            'ver pedidos', 'crear pedidos', 'editar pedidos', 'cancelar pedidos',
            'ver repartidores', 'crear repartidores', 'editar repartidores',
            'ver entregas', 'asignar entregas', 'actualizar entregas',
            'ver pagos', 'registrar pagos',
            'ver reportes',
            'ver usuarios',
        ]);

        // Operador
        $operador->syncPermissions([
            'ver dashboard',
            'ver clientes', 'crear clientes', 'editar clientes',
            'ver productos',
            'ver pedidos', 'crear pedidos', 'editar pedidos',
            'ver repartidores',
            'ver entregas', 'asignar entregas',
            'ver pagos', 'registrar pagos',
        ]);

        // Repartidor
        $repartidor->syncPermissions([
            'ver entregas', 'actualizar entregas',
        ]);

        // Crear usuarios iniciales
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@crm.com'],
            [
                'name'              => 'Super Administrador',
                'password'          => Hash::make('password'),
                'telefono'          => '999000001',
                'activo'            => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('super-admin');

        $adminUser2 = User::firstOrCreate(
            ['email' => 'gerente@crm.com'],
            [
                'name'              => 'Gerente General',
                'password'          => Hash::make('password'),
                'telefono'          => '999000002',
                'activo'            => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser2->assignRole('admin');

        $operadorUser = User::firstOrCreate(
            ['email' => 'operador@crm.com'],
            [
                'name'              => 'María Operadora',
                'password'          => Hash::make('password'),
                'telefono'          => '999000003',
                'activo'            => true,
                'email_verified_at' => now(),
            ]
        );
        $operadorUser->assignRole('operador');

        $repartidorUser = User::firstOrCreate(
            ['email' => 'repartidor@crm.com'],
            [
                'name'              => 'Carlos Repartidor',
                'password'          => Hash::make('password'),
                'telefono'          => '999000004',
                'activo'            => true,
                'email_verified_at' => now(),
            ]
        );
        $repartidorUser->assignRole('repartidor');
    }
}
