<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sucursal;
use App\Models\TipoEquipo;
// use App\Models\Role; // Removed
use Spatie\Permission\Models\Role; // Use Spatie Role

class CleanDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with essential data only.
     */
    public function run(): void
    {
        // 1. Roles y Permisos
        $this->call(RolePermissionSeeder::class);

        // 2. Sucursales Básicas
        $sucursalPrincipal = Sucursal::create([
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Dirección Principal',
            'telefono' => '000-0000',
            'estado' => 'Activo'
        ]);

        // 3. Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventario.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'id_sucursal' => $sucursalPrincipal->id,
                'activo' => 1
            ]
        );

        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        // 4. Catálogos Vacios o Básicos
        TipoEquipo::create(['nombre' => 'Laptop', 'estado' => 'Activo']);
        TipoEquipo::create(['nombre' => 'Desktop', 'estado' => 'Activo']);
        TipoEquipo::create(['nombre' => 'Monitor', 'estado' => 'Activo']);
        TipoEquipo::create(['nombre' => 'Impresora', 'estado' => 'Activo']);
        TipoEquipo::create(['nombre' => 'Periféricos', 'estado' => 'Activo']);

        echo "✅ Base de datos limpia creada exitosamente\n";
        echo "📧 Admin: admin@inventario.com | password: password\n";
    }
}
