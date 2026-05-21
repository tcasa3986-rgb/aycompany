<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Módulos y sus permisos
        $modules = [
            'users' => ['view', 'create', 'edit', 'toggle'], // 'delete' is mostly toggle
            'roles' => ['view', 'create', 'edit', 'delete'],
            'equipos' => ['view', 'create', 'edit', 'delete', 'export'],
            'empleados' => ['view', 'create', 'edit', 'toggle', 'export'],
            'asignaciones' => ['view', 'create', 'edit', 'annul', 'return', 'export'],
            'reparaciones' => ['view', 'create', 'edit', 'delete'], // Delete might be needed?
            'bajas' => ['view', 'create', 'edit', 'delete'],
            'sucursales' => ['view', 'create', 'edit', 'toggle'],
            'areas' => ['view', 'create', 'edit', 'toggle'],
            'cargos' => ['view', 'create', 'edit', 'toggle'],
            'marcas' => ['view', 'create', 'edit', 'toggle'],
            'modelos' => ['view', 'create', 'edit', 'toggle'],
            'tipos_equipo' => ['view', 'create', 'edit', 'toggle'],
            'reportes' => ['view'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$module.$action"]);
            }
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->givePermissionTo(Permission::all());

        // Assign Role to Admin User
        $adminUser = User::where('email', 'admin@inventario.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        } else {
            // Create admin if not exists (safeguard)
            $adminUser = User::create([
                'name' => 'Administrador',
                'email' => 'admin@inventario.com',
                'password' => Hash::make('password'),
                'id_sucursal' => null, // Global access
                'activo' => true,
            ]);
            $adminUser->assignRole($adminRole);
        }
    }
}
