<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AreaLaboratorio;
use App\Models\Prueba;
use App\Models\Paciente;
use App\Models\MedicoReferidor;
use App\Models\Convenio;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Muestra;
use App\Models\Resultado;
use App\Models\Reactivo;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Configuracion;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $roles = ['Administrador', 'Recepcionista', 'Tecnólogo', 'Médico'];
        foreach ($roles as $rol) {
            Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web']);
        }

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@lab.com'],
            [
                'name' => 'Administrador Sistema',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Administrador');

        // Crear usuarios de prueba
        $recepcionista = User::firstOrCreate(
            ['email' => 'recepcion@lab.com'],
            ['name' => 'María García', 'password' => Hash::make('password')]
        );
        $recepcionista->assignRole('Recepcionista');

        $tecnologo = User::firstOrCreate(
            ['email' => 'tecnologo@lab.com'],
            ['name' => 'Carlos Mendoza', 'password' => Hash::make('password')]
        );
        $tecnologo->assignRole('Tecnólogo');

        // Áreas de laboratorio
        $areas = [
            ['nombre' => 'Hematología', 'codigo' => 'HEM', 'descripcion' => 'Análisis de sangre y componentes sanguíneos'],
            ['nombre' => 'Bioquímica', 'codigo' => 'BIO', 'descripcion' => 'Análisis bioquímico y metabólico'],
            ['nombre' => 'Microbiología', 'codigo' => 'MIC', 'descripcion' => 'Cultivos y estudios microbiológicos'],
            ['nombre' => 'Inmunología', 'codigo' => 'INM', 'descripcion' => 'Pruebas inmunológicas y serológicas'],
            ['nombre' => 'Uroanálisis', 'codigo' => 'URO', 'descripcion' => 'Análisis de orina y sedimento urinario'],
            ['nombre' => 'Parasitología', 'codigo' => 'PAR', 'descripcion' => 'Estudio de parásitos intestinales'],
            ['nombre' => 'Hormonología', 'codigo' => 'HOR', 'descripcion' => 'Dosaje hormonal y marcadores tumorales'],
            ['nombre' => 'Toxicología', 'codigo' => 'TOX', 'descripcion' => 'Análisis toxicológicos'],
        ];

        $areasCreadas = [];
        foreach ($areas as $area) {
            $areasCreadas[$area['codigo']] = AreaLaboratorio::firstOrCreate(['codigo' => $area['codigo']], $area);
        }

        // Pruebas clínicas
        $pruebas = [
            // Hematología
            ['area_id' => $areasCreadas['HEM']->id, 'codigo' => 'HEM001', 'nombre' => 'Hemograma Completo', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 25.00, 'unidad' => 'cel/µL', 'valores_referencia' => 'Ver informe'],
            ['area_id' => $areasCreadas['HEM']->id, 'codigo' => 'HEM002', 'nombre' => 'Tiempo de Coagulación', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 20.00, 'unidad' => 'seg'],
            ['area_id' => $areasCreadas['HEM']->id, 'codigo' => 'HEM003', 'nombre' => 'Grupo Sanguíneo y Factor Rh', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 1, 'precio' => 15.00, 'unidad' => ''],
            // Bioquímica
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO001', 'nombre' => 'Glucosa en Ayunas', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 12.00, 'unidad' => 'mg/dL', 'valores_referencia' => '70-100 mg/dL'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO002', 'nombre' => 'Urea', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 12.00, 'unidad' => 'mg/dL', 'valores_referencia' => '10-50 mg/dL'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO003', 'nombre' => 'Creatinina', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 12.00, 'unidad' => 'mg/dL', 'valores_referencia' => '0.6-1.2 mg/dL'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO004', 'nombre' => 'Perfil Lipídico', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 4, 'precio' => 45.00, 'unidad' => 'mg/dL'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO005', 'nombre' => 'TGO / TGP (Transaminasas)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 3, 'precio' => 20.00, 'unidad' => 'U/L'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'BIO006', 'nombre' => 'Hemoglobina Glicosilada (HbA1c)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 4, 'precio' => 35.00, 'unidad' => '%', 'valores_referencia' => '<5.7%'],
            // Inmunología
            ['area_id' => $areasCreadas['INM']->id, 'codigo' => 'INM001', 'nombre' => 'PCR (Proteína C Reactiva)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 18.00, 'unidad' => 'mg/L'],
            ['area_id' => $areasCreadas['INM']->id, 'codigo' => 'INM002', 'nombre' => 'VIH 1/2 (ELISA)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 4, 'precio' => 30.00, 'unidad' => ''],
            ['area_id' => $areasCreadas['INM']->id, 'codigo' => 'INM003', 'nombre' => 'VDRL (Sífilis)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 15.00, 'unidad' => ''],
            // Uroanálisis
            ['area_id' => $areasCreadas['URO']->id, 'codigo' => 'URO001', 'nombre' => 'Examen de Orina Completo', 'muestra_tipo' => 'Orina chorro medio', 'tiempo_resultado' => 1, 'precio' => 10.00, 'unidad' => ''],
            ['area_id' => $areasCreadas['URO']->id, 'codigo' => 'URO002', 'nombre' => 'Urocultivo', 'muestra_tipo' => 'Orina chorro medio', 'tiempo_resultado' => 48, 'precio' => 40.00, 'unidad' => ''],
            // Parasitología
            ['area_id' => $areasCreadas['PAR']->id, 'codigo' => 'PAR001', 'nombre' => 'Examen Coproparasitológico', 'muestra_tipo' => 'Heces', 'tiempo_resultado' => 2, 'precio' => 12.00, 'unidad' => ''],
            // Hormonología
            ['area_id' => $areasCreadas['HOR']->id, 'codigo' => 'HOR001', 'nombre' => 'TSH (Hormona Tiroidea)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 4, 'precio' => 35.00, 'unidad' => 'µIU/mL', 'valores_referencia' => '0.5-5.0 µIU/mL'],
            ['area_id' => $areasCreadas['HOR']->id, 'codigo' => 'HOR002', 'nombre' => 'Testosterona Total', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 4, 'precio' => 40.00, 'unidad' => 'ng/dL'],
            ['area_id' => $areasCreadas['HOR']->id, 'codigo' => 'HOR003', 'nombre' => 'Beta HCG (Embarazo)', 'muestra_tipo' => 'Sangre venosa', 'tiempo_resultado' => 2, 'precio' => 25.00, 'unidad' => 'mIU/mL'],
        ];

        foreach ($pruebas as $prueba) {
            Prueba::firstOrCreate(['codigo' => $prueba['codigo']], $prueba);
        }

        // Médicos referidores
        $medicos = [
            ['nombres' => 'Roberto', 'apellidos' => 'Sánchez Torres', 'especialidad' => 'Medicina General', 'cmp' => 'CMP-12345', 'telefono' => '987654321', 'email' => 'rsanchez@clinica.com'],
            ['nombres' => 'Ana María', 'apellidos' => 'López Castillo', 'especialidad' => 'Endocrinología', 'cmp' => 'CMP-23456', 'telefono' => '987654322', 'email' => 'alopez@clinica.com'],
            ['nombres' => 'Jorge', 'apellidos' => 'Vargas Ríos', 'especialidad' => 'Cardiología', 'cmp' => 'CMP-34567', 'telefono' => '987654323', 'email' => 'jvargas@clinica.com'],
            ['nombres' => 'Patricia', 'apellidos' => 'Huamán Díaz', 'especialidad' => 'Nefrología', 'cmp' => 'CMP-45678', 'telefono' => '987654324', 'email' => 'phuaman@clinica.com'],
            ['nombres' => 'Luis', 'apellidos' => 'Flores Mamani', 'especialidad' => 'Gastroenterología', 'cmp' => 'CMP-56789', 'telefono' => '987654325', 'email' => 'lflores@clinica.com'],
        ];
        foreach ($medicos as $medico) {
            MedicoReferidor::firstOrCreate(['cmp' => $medico['cmp']], $medico);
        }

        // Convenios
        $convenios = [
            ['nombre' => 'Rímac Seguros', 'ruc' => '20102009749', 'tipo' => 'Aseguradora', 'descuento_porcentaje' => 15.00, 'contacto_nombre' => 'Carmen Vega', 'contacto_telefono' => '01-4111111'],
            ['nombre' => 'Pacifico Seguros', 'ruc' => '20112056913', 'tipo' => 'Aseguradora', 'descuento_porcentaje' => 10.00, 'contacto_nombre' => 'Pedro Ruiz', 'contacto_telefono' => '01-5131313'],
            ['nombre' => 'Municipalidad de Miraflores', 'ruc' => '20131312955', 'tipo' => 'Empresa', 'descuento_porcentaje' => 20.00, 'contacto_nombre' => 'Rosa Salinas', 'contacto_telefono' => '01-6170000'],
        ];
        foreach ($convenios as $convenio) {
            Convenio::firstOrCreate(['ruc' => $convenio['ruc']], $convenio);
        }

        // Pacientes de prueba
        $pacientes = [
            ['tipo_documento' => 'DNI', 'numero_documento' => '45678901', 'nombres' => 'Juan Carlos', 'apellido_paterno' => 'Pérez', 'apellido_materno' => 'Gutiérrez', 'fecha_nacimiento' => '1985-03-15', 'sexo' => 'M', 'telefono' => '987001001', 'email' => 'jperez@gmail.com', 'tipo_sangre' => 'O+'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '56789012', 'nombres' => 'María Elena', 'apellido_paterno' => 'Torres', 'apellido_materno' => 'Quispe', 'fecha_nacimiento' => '1990-07-22', 'sexo' => 'F', 'telefono' => '987001002', 'email' => 'metorres@gmail.com', 'tipo_sangre' => 'A+'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '67890123', 'nombres' => 'Pedro Antonio', 'apellido_paterno' => 'Rodríguez', 'apellido_materno' => 'Vargas', 'fecha_nacimiento' => '1978-11-08', 'sexo' => 'M', 'telefono' => '987001003', 'tipo_sangre' => 'B+'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '78901234', 'nombres' => 'Carmen Rosa', 'apellido_paterno' => 'Huanca', 'apellido_materno' => 'Mamani', 'fecha_nacimiento' => '1965-05-30', 'sexo' => 'F', 'telefono' => '987001004', 'tipo_sangre' => 'AB+'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '89012345', 'nombres' => 'Luis Fernando', 'apellido_paterno' => 'Chávez', 'apellido_materno' => 'Mendoza', 'fecha_nacimiento' => '2000-12-01', 'sexo' => 'M', 'telefono' => '987001005', 'tipo_sangre' => 'O-'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '90123456', 'nombres' => 'Ana Lucía', 'apellido_paterno' => 'Flores', 'apellido_materno' => 'Paz', 'fecha_nacimiento' => '1995-09-14', 'sexo' => 'F', 'telefono' => '987001006', 'email' => 'aflores@gmail.com', 'tipo_sangre' => 'A-'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '01234567', 'nombres' => 'Roberto Carlos', 'apellido_paterno' => 'Mamani', 'apellido_materno' => 'Condori', 'fecha_nacimiento' => '1970-04-20', 'sexo' => 'M', 'telefono' => '987001007', 'tipo_sangre' => 'B-'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '12345670', 'nombres' => 'Sofía Valentina', 'apellido_paterno' => 'Ruiz', 'apellido_materno' => 'Salcedo', 'fecha_nacimiento' => '2003-02-28', 'sexo' => 'F', 'telefono' => '987001008', 'tipo_sangre' => 'AB-'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '23456701', 'nombres' => 'Miguel Ángel', 'apellido_paterno' => 'Castro', 'apellido_materno' => 'Benites', 'fecha_nacimiento' => '1988-08-10', 'sexo' => 'M', 'telefono' => '987001009', 'tipo_sangre' => 'O+'],
            ['tipo_documento' => 'DNI', 'numero_documento' => '34567012', 'nombres' => 'Elena Isabel', 'apellido_paterno' => 'Vásquez', 'apellido_materno' => 'Lazo', 'fecha_nacimiento' => '1975-01-17', 'sexo' => 'F', 'telefono' => '987001010', 'email' => 'evasquez@gmail.com', 'tipo_sangre' => 'A+'],
        ];

        $i = 1;
        foreach ($pacientes as $pDatos) {
            $pDatos['historia_clinica'] = 'HC-' . str_pad($i++, 6, '0', STR_PAD_LEFT);
            Paciente::firstOrCreate(['numero_documento' => $pDatos['numero_documento']], $pDatos);
        }

        // Reactivos
        $reactivos = [
            ['area_id' => $areasCreadas['HEM']->id, 'codigo' => 'REA001', 'nombre' => 'Reactivo Hemograma', 'marca' => 'Sysmex', 'proveedor' => 'MedLab Perú', 'unidad_medida' => 'Cartucho', 'stock_actual' => 10, 'stock_minimo' => 3, 'precio_unitario' => 150.00, 'lote' => 'LOT2025A'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'REA002', 'nombre' => 'Reactivo Glucosa', 'marca' => 'Roche', 'proveedor' => 'Diagnostico SA', 'unidad_medida' => 'Frasco 500 mL', 'stock_actual' => 5, 'stock_minimo' => 3, 'precio_unitario' => 80.00, 'lote' => 'LOT2025B'],
            ['area_id' => $areasCreadas['BIO']->id, 'codigo' => 'REA003', 'nombre' => 'Reactivo Creatinina', 'marca' => 'Roche', 'proveedor' => 'Diagnostico SA', 'unidad_medida' => 'Frasco 500 mL', 'stock_actual' => 2, 'stock_minimo' => 3, 'precio_unitario' => 75.00, 'lote' => 'LOT2025C', 'estado' => 'Stock bajo'],
            ['area_id' => $areasCreadas['HOR']->id, 'codigo' => 'REA004', 'nombre' => 'Kit TSH Ultrasensible', 'marca' => 'Abbott', 'proveedor' => 'Abbott Perú', 'unidad_medida' => 'Kit 100 pruebas', 'stock_actual' => 1, 'stock_minimo' => 2, 'precio_unitario' => 450.00, 'lote' => 'LOT2025D', 'estado' => 'Stock bajo'],
            ['area_id' => $areasCreadas['INM']->id, 'codigo' => 'REA005', 'nombre' => 'Kit VDRL', 'marca' => 'Wiener Lab', 'proveedor' => 'Wiener Lab Perú', 'unidad_medida' => 'Kit 100 pruebas', 'stock_actual' => 8, 'stock_minimo' => 2, 'precio_unitario' => 120.00, 'lote' => 'LOT2025E'],
        ];
        foreach ($reactivos as $reactivo) {
            Reactivo::firstOrCreate(['codigo' => $reactivo['codigo']], $reactivo);
        }

        // Órdenes de prueba con detalles
        $this->crearOrdenesPrueba($recepcionista, $tecnologo);

        // Configuraciones
        $configs = [
            ['clave' => 'nombre_laboratorio', 'valor' => 'Laboratorio Clínico LabSalud', 'tipo' => 'texto', 'descripcion' => 'Nombre del laboratorio'],
            ['clave' => 'ruc', 'valor' => '20123456789', 'tipo' => 'texto', 'descripcion' => 'RUC del laboratorio'],
            ['clave' => 'direccion', 'valor' => 'Av. Javier Prado 1234, San Isidro, Lima', 'tipo' => 'texto', 'descripcion' => 'Dirección'],
            ['clave' => 'telefono', 'valor' => '01-2345678', 'tipo' => 'texto', 'descripcion' => 'Teléfono principal'],
            ['clave' => 'email', 'valor' => 'info@labsalud.com', 'tipo' => 'texto', 'descripcion' => 'Email de contacto'],
            ['clave' => 'igv_porcentaje', 'valor' => '18', 'tipo' => 'numero', 'descripcion' => 'Porcentaje de IGV'],
            ['clave' => 'horario_atencion', 'valor' => 'Lun-Vie 7:00-19:00 | Sáb 7:00-14:00', 'tipo' => 'texto', 'descripcion' => 'Horario de atención'],
        ];
        foreach ($configs as $config) {
            Configuracion::firstOrCreate(['clave' => $config['clave']], $config);
        }
    }

    private function crearOrdenesPrueba(User $recepcionista, User $tecnologo): void
    {
        $pacientes = Paciente::all();
        $pruebas = Prueba::all();
        $estados = ['Pendiente', 'En proceso', 'Completado', 'Entregado'];
        $prioridades = ['Normal', 'Normal', 'Normal', 'Urgente'];
        $medicos = MedicoReferidor::all();

        for ($i = 1; $i <= 10; $i++) {
            $paciente = $pacientes->random();
            $medico = $medicos->random();
            $pruebasOrden = $pruebas->random(rand(2, 4));
            $subtotal = $pruebasOrden->sum('precio');
            $estado = $estados[array_rand($estados)];

            $orden = Orden::create([
                'numero_orden' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'user_id' => $recepcionista->id,
                'fecha_registro' => now()->subDays(rand(0, 30)),
                'diagnostico_presuntivo' => 'Control de rutina',
                'estado' => $estado,
                'prioridad' => $prioridades[array_rand($prioridades)],
                'subtotal' => $subtotal,
                'descuento' => 0,
                'total' => $subtotal,
                'pagado' => in_array($estado, ['Completado', 'Entregado']),
            ]);

            foreach ($pruebasOrden as $prueba) {
                $detalle = OrdenDetalle::create([
                    'orden_id' => $orden->id,
                    'prueba_id' => $prueba->id,
                    'precio_unitario' => $prueba->precio,
                    'descuento' => 0,
                    'precio_final' => $prueba->precio,
                    'estado' => $estado === 'Pendiente' ? 'Pendiente' : 'Completado',
                ]);

                // Muestra
                $muestra = Muestra::create([
                    'orden_id' => $orden->id,
                    'codigo_muestra' => 'MUE-' . str_pad($i * 1000 + $prueba->id, 8, '0', STR_PAD_LEFT),
                    'tipo_muestra' => $prueba->muestra_tipo,
                    'fecha_toma' => $orden->fecha_registro->addHours(1),
                    'tomado_por' => $tecnologo->id,
                    'estado' => 'Analizada',
                ]);

                // Resultado si está completado
                if (in_array($estado, ['Completado', 'Entregado'])) {
                    Resultado::create([
                        'orden_detalle_id' => $detalle->id,
                        'muestra_id' => $muestra->id,
                        'valor' => rand(50, 150) . ' ' . ($prueba->unidad ?? ''),
                        'unidad' => $prueba->unidad,
                        'valores_referencia' => $prueba->valores_referencia,
                        'interpretacion' => 'Normal',
                        'metodo' => 'Automático',
                        'equipo' => 'Analizador principal',
                        'validado_por' => $tecnologo->id,
                        'fecha_validacion' => $orden->fecha_registro->addHours(rand(2, 8)),
                        'valor_critico' => false,
                        'notificado' => true,
                    ]);
                }
            }

            // Factura para órdenes pagadas
            if ($orden->pagado) {
                $factura = Factura::create([
                    'orden_id' => $orden->id,
                    'numero_factura' => 'FAC-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'tipo_comprobante' => 'Boleta',
                    'subtotal' => $orden->total,
                    'descuento' => 0,
                    'igv' => 0,
                    'total' => $orden->total,
                    'estado' => 'Pagada',
                    'user_id' => $recepcionista->id,
                ]);

                Pago::create([
                    'factura_id' => $factura->id,
                    'monto' => $orden->total,
                    'medio_pago' => ['Efectivo', 'Tarjeta', 'Transferencia'][rand(0, 2)],
                    'fecha_pago' => $orden->fecha_registro->addHours(1),
                    'user_id' => $recepcionista->id,
                ]);
            }
        }
    }
}
