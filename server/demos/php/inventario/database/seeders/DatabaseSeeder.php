<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sucursal;
use App\Models\TipoEquipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Area;
use App\Models\Cargo;
// use App\Models\Role; // Removed
use App\Models\Equipo;
use App\Models\Empleado;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ejecutar Seeder de Roles y Permisos PRIMERO
        $this->call(RolePermissionSeeder::class);

        // Crear sucursales
        $sucursalPrincipal = Sucursal::create([
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Av. Principal 123',
            'telefono' => '555-0001',
            'estado' => 'Activo'
        ]);

        $sucursalSecundaria = Sucursal::create([
            'nombre' => 'Sucursal Secundaria',
            'direccion' => 'Calle Secundaria 456',
            'telefono' => '555-0002',
            'estado' => 'Activo'
        ]);

        // Crear usuario administrador (si no existe ya por el RolePermissionSeeder)
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventario.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'id_sucursal' => $sucursalPrincipal->id,
                'activo' => 1
            ]
        );
        // Validar que tenga el rol
        if (!$admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }

        // Crear usuario normal
        $user = User::firstOrCreate(
            ['email' => 'usuario@inventario.com'],
            [
                'name' => 'Usuario Normal',
                'password' => Hash::make('password'),
                'id_sucursal' => $sucursalPrincipal->id,
                'activo' => 1
            ]
        );
        // Asignar permisos básicos o un rol 'Usuario' si existiera (RolePermissionSeeder no crea 'Usuario' por defecto, solo Admin. Creemos uno básico si es necesario, o démosle Admin para pruebas)
        // Por ahora, no asignamos rol, o podemos crear un rol 'Soporte' en el seeder.
        // Asumiremos que el RolePermissionSeeder maneja roles.

        // Crear tipos de equipo
        $laptop = TipoEquipo::create(['nombre' => 'Laptop', 'estado' => 'Activo']);
        $desktop = TipoEquipo::create(['nombre' => 'Desktop', 'estado' => 'Activo']);
        $monitor = TipoEquipo::create(['nombre' => 'Monitor', 'estado' => 'Activo']);
        $impresora = TipoEquipo::create(['nombre' => 'Impresora', 'estado' => 'Activo']);

        // Crear marcas y modelos
        $dell = Marca::create(['nombre' => 'Dell', 'estado' => 'Activo']);
        $hp = Marca::create(['nombre' => 'HP', 'estado' => 'Activo']);
        $lenovo = Marca::create(['nombre' => 'Lenovo', 'estado' => 'Activo']);

        Modelo::create(['id_marca' => $dell->id, 'nombre' => 'Latitude 5420', 'estado' => 'Activo']);
        Modelo::create(['id_marca' => $dell->id, 'nombre' => 'OptiPlex 7090', 'estado' => 'Activo']);
        Modelo::create(['id_marca' => $hp->id, 'nombre' => 'EliteBook 840', 'estado' => 'Activo']);
        Modelo::create(['id_marca' => $hp->id, 'nombre' => 'ProDesk 600', 'estado' => 'Activo']);
        Modelo::create(['id_marca' => $lenovo->id, 'nombre' => 'ThinkPad X1', 'estado' => 'Activo']);

        // Crear áreas y cargos
        $sistemas = Area::create(['nombre' => 'Sistemas', 'estado' => 'Activo']);
        $contabilidad = Area::create(['nombre' => 'Contabilidad', 'estado' => 'Activo']);
        $ventas = Area::create(['nombre' => 'Ventas', 'estado' => 'Activo']);

        Cargo::create(['id_area' => $sistemas->id, 'nombre' => 'Desarrollador', 'estado' => 'Activo']);
        Cargo::create(['id_area' => $sistemas->id, 'nombre' => 'Soporte Técnico', 'estado' => 'Activo']);
        Cargo::create(['id_area' => $contabilidad->id, 'nombre' => 'Contador', 'estado' => 'Activo']);
        Cargo::create(['id_area' => $ventas->id, 'nombre' => 'Ejecutivo de Ventas', 'estado' => 'Activo']);

        // Crear algunos equipos
        for ($i = 1; $i <= 20; $i++) {
            Equipo::create([
                'id_sucursal' => $i % 2 == 0 ? $sucursalPrincipal->id : $sucursalSecundaria->id,
                'codigo_inventario' => 'EQ-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'id_tipo_equipo' => [1, 2, 3, 4][array_rand([1, 2, 3, 4])],
                'id_marca' => [1, 2, 3][array_rand([1, 2, 3])],
                'id_modelo' => rand(1, 5),
                'numero_serie' => 'SN' . strtoupper(bin2hex(random_bytes(8))),
                'caracteristicas' => 'Intel i5, 8GB RAM, 256GB SSD',
                'tipo_adquisicion' => ['Propio', 'Arrendado', 'Prestamo'][array_rand(['Propio', 'Arrendado', 'Prestamo'])],
                'fecha_adquisicion' => now()->subDays(rand(30, 365)),
                'proveedor' => 'Proveedor ' . rand(1, 3),
                'estado' => ['Disponible', 'Asignado', 'En Reparacion'][array_rand(['Disponible', 'Asignado', 'En Reparacion'])],
                'observaciones' => 'Observaciones del equipo ' . $i
            ]);
        }

        // Crear algunos empleados
        $cargos = [1, 2, 3, 4];
        for ($i = 1; $i <= 15; $i++) {
            Empleado::create([
                'id_sucursal' => $i % 2 == 0 ? $sucursalPrincipal->id : $sucursalSecundaria->id,
                'dni' => '7' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'nombres' => 'Empleado ' . $i,
                'apellidos' => 'Apellido ' . $i,
                'id_cargo' => $cargos[array_rand($cargos)],
                'id_area' => rand(1, 3),
                'estado' => 'Activo'
            ]);
        }

        echo "✅ Datos de prueba creados exitosamente\n";
        echo "📧 Admin: admin@inventario.com | password: password\n";
        echo "📧 Usuario: usuario@inventario.com | password: password\n";
    }
}
