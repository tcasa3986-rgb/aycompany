<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Analgésicos',     'descripcion' => 'Para alivio del dolor'],
            ['nombre' => 'Antibióticos',    'descripcion' => 'Tratamiento de infecciones'],
            ['nombre' => 'Antialérgicos',   'descripcion' => 'Tratamiento de alergias'],
            ['nombre' => 'Antiinflamatorios','descripcion' => 'Reducen la inflamación'],
            ['nombre' => 'Vitaminas',       'descripcion' => 'Suplementos vitamínicos'],
            ['nombre' => 'Cosméticos',      'descripcion' => 'Cuidado personal y belleza'],
            ['nombre' => 'Insumos médicos', 'descripcion' => 'Material e insumos'],
            ['nombre' => 'Cuidado infantil','descripcion' => 'Productos para bebés'],
        ];

        foreach ($categorias as $c) {
            Categoria::firstOrCreate(['nombre' => $c['nombre']], $c);
        }
    }
}
