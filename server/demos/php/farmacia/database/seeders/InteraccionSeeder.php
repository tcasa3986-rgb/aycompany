<?php

namespace Database\Seeders;

use App\Models\Interaccion;
use Illuminate\Database\Seeder;

class InteraccionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Paracetamol', 'Ibuprofeno', 'baja',
                'Combinación habitualmente segura, pero puede aumentar riesgo gastrointestinal con uso prolongado.'],
            ['Amoxicilina', 'Loratadina', 'baja',
                'Sin interacciones clínicamente significativas reportadas.'],
            ['Ibuprofeno', 'Ácido ascórbico', 'baja',
                'No hay interacciones relevantes.'],
            ['Paracetamol', 'Cetirizina', 'baja',
                'No hay interacciones relevantes.'],
            ['Ibuprofeno', 'Cetirizina', 'baja',
                'No hay interacciones relevantes.'],
            // Reglas más críticas (didácticas)
            ['Paracetamol', 'Paracetamol', 'severa',
                'Riesgo de hepatotoxicidad por dosis acumulada. Verificar otras presentaciones que contengan el mismo principio.'],
            ['Ibuprofeno', 'Aspirina', 'severa',
                'Reduce el efecto cardioprotector de la aspirina y aumenta el riesgo gastrointestinal.'],
            ['Warfarina', 'Aspirina', 'severa',
                'Riesgo aumentado de sangrado. Requiere ajuste y monitoreo estrecho.'],
        ];

        foreach ($rows as [$a, $b, $s, $d]) {
            Interaccion::updateOrCreate(
                ['principio_a' => $a, 'principio_b' => $b],
                ['severidad' => $s, 'descripcion' => $d]
            );
        }
    }
}
