<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Prueba;
use App\Models\User;
use App\Models\MedicoReferidor;
use App\Models\Muestra;
use App\Models\Resultado;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Convenio;
use App\Models\AreaLaboratorio;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
            ]);
        }

        $areas = AreaLaboratorio::all();
        if ($areas->isEmpty()) {
            AreaLaboratorio::create(['nombre' => 'General', 'codigo' => 'GEN']);
            $areas = AreaLaboratorio::all();
        }

        // 1. Agregar 10 Pruebas adicionales
        for ($i = 0; $i < 10; $i++) {
            Prueba::create([
                'codigo' => 'TST-' . str_pad($i + 20, 3, '0', STR_PAD_LEFT),
                'nombre' => 'Prueba Demo ' . ($i + 1),
                'area_id' => $areas->random()->id,
                'muestra_tipo' => 'Sangre',
                'tiempo_resultado' => rand(1, 3),
                'precio' => rand(10, 100),
                'unidad' => 'mg/dL',
                'activo' => true
            ]);
        }

        // 2. Agregar 10 Médicos Referidores adicionales
        for ($i = 0; $i < 10; $i++) {
            MedicoReferidor::create([
                'nombres' => 'Dr. Demo ' . ($i + 1),
                'apellidos' => 'Apellido ' . Str::random(5),
                'especialidad' => 'Medicina General',
                'cmp' => 'CMP-' . rand(10000, 99999),
                'telefono' => '9' . rand(10000000, 99999999),
                'email' => "medico{$i}@demo.com"
            ]);
        }

        // 3. Agregar 10 Convenios adicionales
        for ($i = 0; $i < 10; $i++) {
            Convenio::create([
                'nombre' => 'Aseguradora Demo ' . ($i + 1),
                'ruc' => '20' . rand(100000000, 999999999),
                'tipo' => 'Aseguradora',
                'descuento_porcentaje' => rand(5, 25),
                'contacto_nombre' => 'Contacto ' . ($i + 1)
            ]);
        }

        // 4. Agregar 10 Pacientes adicionales
        for ($i = 0; $i < 10; $i++) {
            $numDoc = rand(10000000, 99999999);
            Paciente::create([
                'tipo_documento' => 'DNI',
                'numero_documento' => $numDoc,
                'nombres' => 'Paciente Demo ' . ($i + 1),
                'apellido_paterno' => 'ApellidoP ' . ($i + 1),
                'apellido_materno' => 'ApellidoM ' . ($i + 1),
                'historia_clinica' => 'HC-' . str_pad(rand(10000, 99999), 6, '0', STR_PAD_LEFT),
                'fecha_nacimiento' => Carbon::now()->subYears(rand(18, 70))->format('Y-m-d'),
                'sexo' => rand(0, 1) ? 'M' : 'F',
                'email' => "paciente{$i}@demo.com",
                'telefono' => '9' . rand(10000000, 99999999),
            ]);
        }

        $pacientes = Paciente::latest()->take(10)->get();
        $pruebas = Prueba::all();
        $medicos = MedicoReferidor::all();
        $convenios = Convenio::all();

        // 5. Agregar 10 Órdenes (con sus facturas)
        $estados = ['Pendiente', 'En proceso', 'Completado', 'Entregado'];
        
        for ($i = 0; $i < 10; $i++) {
            $paciente = $pacientes[$i];
            $medico = $medicos->random();
            $convenio = rand(0, 1) ? $convenios->random() : null;
            
            // Distribuir fechas en los últimos 6 meses para los gráficos
            $fecha = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 25));
            $estado = ($fecha->isCurrentMonth()) ? $estados[array_rand($estados)] : 'Completado';
            
            $pruebasSeleccionadas = $pruebas->random(rand(1, 3));
            $subtotal = $pruebasSeleccionadas->sum('precio');
            $descuento = $convenio ? ($subtotal * ($convenio->descuento_porcentaje / 100)) : 0;
            $total = $subtotal - $descuento;

            $orden = Orden::create([
                'numero_orden' => 'ORD-' . strtoupper(Str::random(8)),
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'convenio_id' => $convenio ? $convenio->id : null,
                'user_id' => $user->id,
                'fecha_registro' => $fecha,
                'diagnostico_presuntivo' => 'Control de datos demo',
                'prioridad' => rand(0, 3) == 3 ? 'Urgente' : 'Normal',
                'estado' => $estado,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'pagado' => ($estado == 'Completado' || rand(0, 1)),
            ]);

            foreach ($pruebasSeleccionadas as $prueba) {
                $detalle = OrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'prueba_id' => $prueba->id,
                    'precio_unitario' => $prueba->precio,
                    'descuento' => $convenio ? ($prueba->precio * ($convenio->descuento_porcentaje / 100)) : 0,
                    'precio_final' => $convenio ? ($prueba->precio * (1 - $convenio->descuento_porcentaje / 100)) : $prueba->precio,
                    'estado' => $estado,
                ]);

                // Muestra siempre se toma
                $muestra = Muestra::create([
                    'orden_id' => $orden->id,
                    'codigo_muestra' => 'MUE-' . strtoupper(Str::random(10)),
                    'tipo_muestra' => $prueba->muestra_tipo,
                    'fecha_toma' => $fecha->copy()->addMinutes(30),
                    'tomado_por' => $user->id,
                    'estado' => ($estado == 'Pendiente') ? 'Recibida' : 'Analizada',
                ]);

                // Resultado solo si está completada
                if ($estado == 'Completado' || $estado == 'Entregado') {
                    Resultado::create([
                        'orden_detalle_id' => $detalle->id,
                        'muestra_id' => $muestra->id,
                        'valor' => rand(5, 50) . '.0',
                        'unidad' => $prueba->unidad,
                        'valores_referencia' => $prueba->valores_referencia,
                        'interpretacion' => 'Normal',
                        'metodo' => 'Automático',
                        'validado_por' => $user->id,
                        'fecha_validacion' => $fecha->copy()->addHours(3),
                        'valor_critico' => false
                    ]);
                }
            }

            // Factura y Pago
            if ($orden->pagado) {
                $factura = Factura::create([
                    'orden_id' => $orden->id,
                    'numero_factura' => 'BC-' . str_pad(rand(1, 999999), 8, '0', STR_PAD_LEFT),
                    'tipo_comprobante' => 'Boleta',
                    'convenio_id' => $orden->convenio_id,
                    'subtotal' => $orden->subtotal,
                    'descuento' => $orden->descuento,
                    'igv' => $orden->total * 0.18,
                    'total' => $orden->total,
                    'estado' => 'Pagada',
                    'user_id' => $user->id,
                ]);

                Pago::create([
                    'factura_id' => $factura->id,
                    'monto' => $orden->total,
                    'medio_pago' => 'Efectivo',
                    'fecha_pago' => $fecha->copy()->addHours(1),
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
