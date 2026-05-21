<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            'dashboard.view',
            'inventario.view',   'inventario.manage',
            'categorias.manage', 'proveedores.manage',
            'lotes.manage',
            'clientes.view',     'clientes.manage',
            'pos.use',
            'caja.use',          'caja.cerrar',
            'compras.view',      'compras.manage',
            'recetas.view',      'recetas.manage',
            'reportes.view',     'reportes.export',
            'usuarios.manage',
            'settings.manage',
            'audit.view',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $roles = [
            'Administrador'  => $permisos,
            'Farmaceutico'   => ['dashboard.view',
                                  'inventario.view', 'inventario.manage', 'lotes.manage',
                                  'clientes.view', 'clientes.manage',
                                  'recetas.view', 'recetas.manage',
                                  'pos.use', 'caja.use'],
            'Cajero'         => ['dashboard.view', 'clientes.view', 'pos.use', 'caja.use'],
            'Almacenero'     => ['dashboard.view',
                                  'inventario.view', 'inventario.manage',
                                  'categorias.manage', 'proveedores.manage', 'lotes.manage',
                                  'compras.view', 'compras.manage'],
            'Contador'       => ['dashboard.view', 'reportes.view', 'reportes.export', 'caja.cerrar'],
        ];

        foreach ($roles as $rol => $perms) {
            $role = Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
