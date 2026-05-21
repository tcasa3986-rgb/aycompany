<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Empleado;
use App\Models\Asignacion;
use App\Models\Reparacion;
use App\Models\Sucursal;
use App\Models\TipoEquipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Area;
use App\Models\Cargo;
use Carbon\Carbon;

class DashboardTestSeeder extends Seeder
{
    public function run()
    {
        // Ensure dependencies exist
        $sucursal = Sucursal::first() ?? Sucursal::create(['nombre' => 'Sucursal Central', 'direccion' => 'Av. Test 123', 'telefono' => '123456789', 'estado' => 'Activo']);
        $area = Area::first() ?? Area::create(['nombre' => 'TI', 'descripcion' => 'Tecnología', 'id_sucursal' => $sucursal->id, 'estado' => 'Activo']);
        $cargo = Cargo::first() ?? Cargo::create(['nombre' => 'Desarrollador', 'descripcion' => 'Dev', 'id_area' => $area->id, 'estado' => 'Activo']);
        $marca = Marca::first() ?? Marca::create(['nombre' => 'Dell', 'estado' => 'Activo']);
        $tipo = TipoEquipo::first() ?? TipoEquipo::create(['nombre' => 'Laptop', 'descripcion' => 'Portátil', 'estado' => 'Activo']);
        $modelo = Modelo::first() ?? Modelo::create(['nombre' => 'Latitude 5420', 'id_marca' => $marca->id, 'id_tipo_equipo' => $tipo->id, 'estado' => 'Activo']);

        // Create Employees
        $empleado1 = Empleado::create([
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'dni' => '12345678',
            'id_cargo' => $cargo->id,
            'id_sucursal' => $sucursal->id,
            'estado' => 'Activo'
        ]);

        $empleado2 = Empleado::create([
            'nombres' => 'Maria',
            'apellidos' => 'Gomez',
            'dni' => '87654321',
            'id_cargo' => $cargo->id,
            'id_sucursal' => $sucursal->id,
            'estado' => 'Activo'
        ]);

        // Create Equipment
        $equipo1 = Equipo::create([
            'codigo_inventario' => 'LAP-001',
            'id_tipo_equipo' => $tipo->id,
            'id_marca' => $marca->id,
            'id_modelo' => $modelo->id,
            'numero_serie' => 'SN001',
            'estado' => 'Asignado', // Will be assigned
            'id_sucursal' => $sucursal->id,
            'tipo_adquisicion' => 'Propio'
        ]);

        $equipo2 = Equipo::create([
            'codigo_inventario' => 'LAP-002',
            'id_tipo_equipo' => $tipo->id,
            'id_marca' => $marca->id,
            'id_modelo' => $modelo->id,
            'numero_serie' => 'SN002',
            'estado' => 'En Reparacion', // Will be processed
            'id_sucursal' => $sucursal->id,
            'tipo_adquisicion' => 'Propio'
        ]);

        // Create Assignment (Active)
        Asignacion::create([
            'id_equipo' => $equipo1->id,
            'id_empleado' => $empleado1->id,
            'fecha_entrega' => Carbon::now()->subDays(2),
            'estado_asignacion' => 'Activa',
            'observaciones_entrega' => 'Asignación de prueba'
        ]);

        // Create Repair (Pending, This Month)
        Reparacion::create([
            'id_equipo' => $equipo2->id,
            'fecha_ingreso' => Carbon::now()->subDays(1),
            'motivo' => 'Pantalla rota',
            'proveedor_servicio' => 'Soporte Externo SAC',
            'costo' => 150.00,
            'estado_reparacion' => 'En Proceso',
            'created_at' => Carbon::now() // Ensure it is created this month
        ]);

        $this->command->info('Dashboard Data Seeded Successfully!');
    }
}
