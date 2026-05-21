<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\ConceptoPago;
use App\Models\Personal;
use App\Models\Alumno;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\Materia;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── USUARIOS ──
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@colegio.edu.pe',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'activo'   => true,
        ]);

        User::create([
            'name'     => 'María García López',
            'email'    => 'secretaria@colegio.edu.pe',
            'password' => Hash::make('admin123'),
            'role'     => 'secretaria',
            'activo'   => true,
        ]);

        // ── GRADOS ──
        $grados = [];
        foreach (['1er Grado', '2do Grado', '3er Grado', '4to Grado', '5to Grado', '6to Grado'] as $nombre) {
            $grados[] = Grado::create(['nombre' => $nombre, 'nivel' => 'primaria']);
        }
        foreach (['1er Año', '2do Año', '3er Año', '4to Año', '5to Año'] as $nombre) {
            $grados[] = Grado::create(['nombre' => $nombre, 'nivel' => 'secundaria']);
        }

        // ── SECCIONES ──
        $secciones = [];
        foreach ($grados as $g) {
            $secciones[] = Seccion::create(['grado_id' => $g->id, 'nombre' => 'A', 'turno' => 'mañana', 'capacidad' => 30]);
            if ($g->nivel == 'primaria') {
                $secciones[] = Seccion::create(['grado_id' => $g->id, 'nombre' => 'B', 'turno' => 'tarde', 'capacidad' => 25]);
            }
        }

        // ── CONCEPTOS DE PAGO ──
        ConceptoPago::create(['nombre' => 'Matrícula 2026', 'monto' => 450.00, 'tipo' => 'matricula', 'activo' => true]);
        $mensualidad = ConceptoPago::create(['nombre' => 'Mensualidad General', 'monto' => 300.00, 'tipo' => 'mensualidad', 'activo' => true]);

        // ── PERSONAL ──
        $nombres = ['Juan', 'Maria', 'Pedro', 'Ana', 'Luis', 'Carmen', 'Jose', 'Elena', 'Ricardo', 'Sofia'];
        $apellidos = ['Perez', 'Gomez', 'Rodriguez', 'Sanches', 'Lopez', 'Torres', 'Diaz', 'Vargas', 'Mendoza', 'Castro'];
        
        for ($i = 0; $i < 10; $i++) {
            Personal::create([
                'dni' => rand(10000000, 99999999),
                'nombres' => $nombres[$i],
                'apellidos' => $apellidos[$i],
                'tipo' => 'docente',
                'especialidad' => 'General',
                'telefono' => '9' . rand(10000000, 99999999),
                'email' => strtolower($nombres[$i]) . '@colegio.edu.pe',
                'fecha_ingreso' => date('Y-m-d', strtotime('-' . rand(1, 5) . ' years')),
                'salario' => rand(2000, 3500),
                'estado' => 'activo'
            ]);
        }

        // ── ALUMNOS, MATRÍCULAS Y PAGOS (REPARTIDOS EN EL TIEMPO) ──
        $anio = (int) date('Y');
        
        for ($i = 1; $i <= 12; $i++) {
            $nombreAlumno = $nombres[rand(0, 9)] . ' ' . $apellidos[rand(0, 9)];
            $alumno = Alumno::create([
                'codigo' => 'ALU' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'dni' => rand(10000000, 99999999),
                'nombres' => explode(' ', $nombreAlumno)[0],
                'apellidos' => explode(' ', $nombreAlumno)[1] . ' ' . $apellidos[rand(0,9)],
                'fecha_nacimiento' => date('Y-m-d', strtotime('-' . rand(6, 15) . ' years')),
                'genero' => rand(0, 1) ? 'M' : 'F',
                'apoderado_nombre' => 'Apoderado de ' . $nombreAlumno,
                'apoderado_telefono' => '9' . rand(10000000, 99999999),
                'apoderado_parentesco' => 'Padre',
                'estado' => 'activo'
            ]);

            $seccion = $secciones[rand(0, count($secciones) - 1)];
            Matricula::create([
                'numero' => 'MAT' . $anio . str_pad($i, 4, '0', STR_PAD_LEFT),
                'alumno_id' => $alumno->id,
                'grado_id' => $seccion->grado_id,
                'seccion_id' => $seccion->id,
                'anio_escolar' => $anio,
                'fecha_matricula' => date('Y-01-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT)),
                'estado' => 'activo',
                'registrado_por' => $admin->id
            ]);

            // Pagos distribuidos por meses para el gráfico del dashboard
            for ($mes = 1; $mes <= 5; $mes++) {
                $monto = 300.00;
                $pagado = rand(0, 1) ? $monto : 0; 
                
                Pago::create([
                    'numero_recibo' => 'REC' . $anio . str_pad(($i * 10) + $mes, 5, '0', STR_PAD_LEFT),
                    'alumno_id' => $alumno->id,
                    'concepto_id' => $mensualidad->id,
                    'anio_escolar' => $anio,
                    'mes' => $mes,
                    'monto' => $monto,
                    'descuento' => 0,
                    'monto_pagado' => $pagado,
                    'fecha_pago' => $pagado > 0 ? date("Y-0$mes-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT)) : date("Y-m-d"), // Usar hoy si no ha pagado
                    'metodo_pago' => $pagado > 0 ? 'efectivo' : 'transferencia',
                    'estado' => $pagado > 0 ? 'pagado' : 'pendiente',
                    'registrado_por' => $admin->id
                ]);
            }
        }

        $this->command->info('✅ Base de datos poblada con éxito para demostración.');
    }
}
