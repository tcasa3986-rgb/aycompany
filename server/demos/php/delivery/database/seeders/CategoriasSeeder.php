<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Comida Rápida',    'slug' => 'comida-rapida',    'icono' => 'bi-egg-fried',     'color' => '#FF6B35', 'orden' => 1],
            ['nombre' => 'Bebidas',          'slug' => 'bebidas',          'icono' => 'bi-cup-hot',       'color' => '#007BFF', 'orden' => 2],
            ['nombre' => 'Postres',          'slug' => 'postres',          'icono' => 'bi-cake2',         'color' => '#E83E8C', 'orden' => 3],
            ['nombre' => 'Ensaladas',        'slug' => 'ensaladas',        'icono' => 'bi-flower1',       'color' => '#28A745', 'orden' => 4],
            ['nombre' => 'Pizzas',           'slug' => 'pizzas',           'icono' => 'bi-circle-square', 'color' => '#DC3545', 'orden' => 5],
            ['nombre' => 'Pollos y Carnes',  'slug' => 'pollos-carnes',    'icono' => 'bi-egg',           'color' => '#FFC107', 'orden' => 6],
            ['nombre' => 'Menús del Día',    'slug' => 'menus-del-dia',    'icono' => 'bi-journal-text',  'color' => '#6F42C1', 'orden' => 7],
            ['nombre' => 'Comida Marina',    'slug' => 'comida-marina',    'icono' => 'bi-droplet',       'color' => '#17A2B8', 'orden' => 8],
            ['nombre' => 'Desayunos',        'slug' => 'desayunos',        'icono' => 'bi-sun',           'color' => '#F0AD4E', 'orden' => 9],
            ['nombre' => 'Otros',            'slug' => 'otros',            'icono' => 'bi-box',           'color' => '#6C757D', 'orden' => 10],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['slug' => $cat['slug']], array_merge($cat, ['activo' => true]));
        }
    }
}
