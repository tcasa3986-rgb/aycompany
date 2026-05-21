<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use App\Models\Orden;
use App\Models\OrdenDetalle;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_PE');

        // 1. Áreas de Laboratorio (10)
        $areas = ['Hematología', 'Bioquímica', 'Inmunología', 'Microbiología', 'Parasitología', 'Urianálisis', 'Endocrinología', 'Marcadores Tumorales', 'Toxicología', 'Citogenética'];
        $areaIds = [];
        foreach ($areas as $idx => $area) {
            $areaIds[] = DB::table('areas_laboratorio')->insertGetId([
                'nombre' => $area,
                'codigo' => strtoupper(substr($area, 0, 3)) . '-' . rand(10, 99),
                'descripcion' => "Área especializada en " . strtolower($area),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Pruebas (10)
        $pruebasData = [
            ['Hemograma Completo', $areaIds[0], 25.00, 'Sangre EDTA'],
            ['Glucosa en Ayunas', $areaIds[1], 15.00, 'Suero'],
            ['Perfil Lipídico', $areaIds[1], 45.00, 'Suero'],
            ['Examen Completo de Orina', $areaIds[5], 12.00, 'Orina'],
            ['Perfil Tiroideo', $areaIds[6], 85.00, 'Suero'],
            ['VIH - Quimioluminiscencia', $areaIds[2], 60.00, 'Suero'],
            ['Heces Parasitológico', $areaIds[4], 18.00, 'Heces'],
            ['PSA Libre y Total', $areaIds[7], 110.00, 'Suero'],
            ['Cultivo y Antibiograma', $areaIds[3], 70.00, 'Muestra diversa'],
            ['Hemoglobina Glicosilada', $areaIds[1], 35.00, 'Sangre EDTA'],
        ];
        $pruebaIds = [];
        foreach ($pruebasData as $idx => $prueba) {
            $pruebaIds[] = DB::table('pruebas')->insertGetId([
                'area_id' => $prueba[1],
                'codigo' => 'PRB-' . strtoupper($faker->bothify('??####')),
                'nombre' => $prueba[0],
                'precio' => $prueba[2],
                'muestra_tipo' => $prueba[3],
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Pacientes (10)
        $pacienteIds = [];
        for ($i = 0; $i < 10; $i++) {
            $pacienteIds[] = DB::table('pacientes')->insertGetId([
                'historia_clinica' => 'HC-' . $faker->unique()->numerify('######'),
                'tipo_documento' => 'DNI',
                'numero_documento' => $faker->unique()->numerify('########'),
                'nombres' => $faker->firstName,
                'apellido_paterno' => $faker->lastName,
                'apellido_materno' => $faker->lastName,
                'fecha_nacimiento' => $faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
                'sexo' => $faker->randomElement(['M', 'F']),
                'telefono' => '9' . $faker->numerify('########'),
                'email' => $faker->unique()->safeEmail,
                'direccion' => $faker->address,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Médicos Referidores (10)
        $medicoIds = [];
        $especialidades = ['Cardiología', 'Medicina General', 'Ginecología', 'Pediatría', 'Endocrinología', 'Medicina Interna', 'Gastroenterología'];
        for ($i = 0; $i < 10; $i++) {
            $medicoIds[] = DB::table('medicos_referidores')->insertGetId([
                'nombres' => $faker->firstName,
                'apellidos' => $faker->lastName . ' ' . $faker->lastName,
                'especialidad' => $faker->randomElement($especialidades),
                'cmp' => 'CMP-' . $faker->numerify('#####'),
                'telefono' => '9' . $faker->numerify('########'),
                'email' => $faker->unique()->safeEmail,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Convenios (10)
        $convenioIds = [];
        for ($i = 0; $i < 10; $i++) {
            $convenioIds[] = DB::table('convenios')->insertGetId([
                'nombre' => $faker->company . ' EPS',
                'ruc' => '20' . $faker->unique()->numerify('#########'),
                'tipo' => $faker->randomElement(['Aseguradora', 'Empresa', 'Clínica']),
                'descuento_porcentaje' => $faker->randomElement([5, 10, 15, 20]),
                'condiciones' => 'Pago a 30 días',
                'contacto_nombre' => $faker->name,
                'contacto_telefono' => '9' . $faker->numerify('########'),
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $convenioIds[] = null; // Add null for some orders

        // 6. Inventario Reactivos (10)
        for ($i = 0; $i < 10; $i++) {
            DB::table('reactivos')->insert([
                'area_id' => $faker->randomElement($areaIds),
                'codigo' => 'RCT-' . $faker->bothify('##??'),
                'nombre' => 'Reactivo ' . $faker->word . ' ' . $faker->bothify('??-##'),
                'marca' => $faker->randomElement(['Roche', 'Abbott', 'Siemens', 'Beckman', 'Sysmex']),
                'proveedor' => $faker->company,
                'lote' => $faker->bothify('LOT-####??'),
                'unidad_medida' => $faker->randomElement(['ml', 'mg', 'unidades', 'cajas']),
                'precio_unitario' => $faker->randomFloat(2, 5, 200),
                'fecha_vencimiento' => $faker->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
                'stock_actual' => $faker->numberBetween(10, 100),
                'stock_minimo' => 15,
                'estado' => 'Disponible',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Citas / Agenda (10)
        for ($i = 0; $i < 10; $i++) {
            DB::table('citas')->insert([
                'paciente_id' => $faker->randomElement($pacienteIds),
                'medico_id' => $faker->randomElement($medicoIds),
                'fecha_hora' => $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d H:i:s'),
                'tipo_atencion' => $faker->randomElement(['Presencial', 'Domicilio']),
                'motivo' => 'Chequeo médico de rutina',
                'estado' => $faker->randomElement(['Programada']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 8. Órdenes (distributed over 6 months to see chart trends) (50 orders to distribute well)
        $adminId = DB::table('users')->first()->id ?? 1;
        
        for ($i = 0; $i < 50; $i++) {
            // Distribute over last 6 months
            $randomMonthOffset = $faker->numberBetween(0, 5);
            $fecha_creacion = Carbon::now()->subMonths($randomMonthOffset)->subDays($faker->numberBetween(0, 28));
            
            $esPagado = $faker->boolean(80); // 80% pagadas
            
            $ordenId = DB::table('ordenes')->insertGetId([
                'numero_orden' => 'ORD-GEN-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'paciente_id' => $faker->randomElement($pacienteIds),
                'medico_id' => $faker->randomElement($medicoIds),
                'convenio_id' => $faker->randomElement($convenioIds), // Might be null
                'user_id' => $adminId,
                'fecha_registro' => $fecha_creacion,
                'diagnostico_presuntivo' => $faker->sentence(3),
                'estado' => $faker->randomElement(['Pendiente', 'En proceso', 'Completado', 'Entregado']),
                'prioridad' => $faker->randomElement(['Normal', 'Urgente', 'Emergencia']),
                'subtotal' => 0, // Will update later
                'descuento' => 0,
                'total' => 0, // Will update later
                'pagado' => $esPagado,
                'created_at' => $fecha_creacion,
                'updated_at' => $fecha_creacion,
            ]);

            // Create detalles (1 to 4 tests per order)
            $cantPruebas = $faker->numberBetween(1, 4);
            $subtotal = 0;
            
            // Randomly select tests without repetition
            $pruebasSeleccionadas = $faker->randomElements($pruebaIds, $cantPruebas);
            
            foreach ($pruebasSeleccionadas as $pId) {
                // Get price from original array based on ID
                $idx = array_search($pId, $pruebaIds);
                $precio = $pruebasData[$idx][2];
                $subtotal += $precio;
                
                DB::table('orden_detalles')->insert([
                    'orden_id' => $ordenId,
                    'prueba_id' => $pId,
                    'precio_unitario' => $precio,
                    'precio_final' => $precio,
                    'estado' => $faker->randomElement(['Pendiente', 'Completado']),
                    'created_at' => $fecha_creacion,
                    'updated_at' => $fecha_creacion,
                ]);
            }
            
            // Generate simple factura if pagado
            if ($esPagado) {
                DB::table('facturas')->insert([
                    'orden_id' => $ordenId,
                    'numero_factura' => 'F' . str_pad($ordenId, 4, '0', STR_PAD_LEFT) . '-' . $faker->numerify('####'),
                    'tipo_comprobante' => $faker->randomElement(['Boleta', 'Factura']),
                    'subtotal' => $subtotal,
                    'descuento' => 0,
                    'igv' => 0,
                    'total' => $subtotal,
                    'estado' => 'Pagada',
                    'user_id' => $adminId,
                    'created_at' => $fecha_creacion,
                    'updated_at' => $fecha_creacion,
                ]);
            }

            // Update subtotal/total of order
            DB::table('ordenes')->where('id', $ordenId)->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
        }
        
    }
}
