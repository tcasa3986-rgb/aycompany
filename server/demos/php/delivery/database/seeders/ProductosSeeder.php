<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $comidaId   = Categoria::where('slug', 'comida-rapida')->value('id');
        $bebidasId  = Categoria::where('slug', 'bebidas')->value('id');
        $postresId  = Categoria::where('slug', 'postres')->value('id');
        $pizzasId   = Categoria::where('slug', 'pizzas')->value('id');
        $pollosId   = Categoria::where('slug', 'pollos-carnes')->value('id');
        $menusId    = Categoria::where('slug', 'menus-del-dia')->value('id');

        $productos = [
            // Comida Rápida
            ['categoria_id' => $comidaId, 'codigo' => 'CF001', 'nombre' => 'Hamburguesa Clásica', 'precio' => 18.00, 'precio_delivery' => 20.00],
            ['categoria_id' => $comidaId, 'codigo' => 'CF002', 'nombre' => 'Hamburguesa Especial', 'precio' => 25.00, 'precio_delivery' => 27.00],
            ['categoria_id' => $comidaId, 'codigo' => 'CF003', 'nombre' => 'Hot Dog Completo', 'precio' => 12.00, 'precio_delivery' => 13.00],
            ['categoria_id' => $comidaId, 'codigo' => 'CF004', 'nombre' => 'Papas Fritas Grandes', 'precio' => 10.00, 'precio_delivery' => 11.00],
            // Bebidas
            ['categoria_id' => $bebidasId, 'codigo' => 'BEB001', 'nombre' => 'Gaseosa 1.5L', 'precio' => 7.00, 'precio_delivery' => 8.00],
            ['categoria_id' => $bebidasId, 'codigo' => 'BEB002', 'nombre' => 'Jugo Natural 500ml', 'precio' => 8.00, 'precio_delivery' => 9.00],
            ['categoria_id' => $bebidasId, 'codigo' => 'BEB003', 'nombre' => 'Agua Mineral 625ml', 'precio' => 3.00, 'precio_delivery' => 3.50],
            ['categoria_id' => $bebidasId, 'codigo' => 'BEB004', 'nombre' => 'Chicha Morada 1L', 'precio' => 6.00, 'precio_delivery' => 7.00],
            // Pizzas
            ['categoria_id' => $pizzasId, 'codigo' => 'PIZ001', 'nombre' => 'Pizza Margarita (personal)', 'precio' => 20.00, 'precio_delivery' => 22.00],
            ['categoria_id' => $pizzasId, 'codigo' => 'PIZ002', 'nombre' => 'Pizza Especial (familiar)', 'precio' => 45.00, 'precio_delivery' => 47.00],
            // Pollos
            ['categoria_id' => $pollosId, 'codigo' => 'POL001', 'nombre' => 'Pollo a la Brasa (1/4)', 'precio' => 18.00, 'precio_delivery' => 20.00],
            ['categoria_id' => $pollosId, 'codigo' => 'POL002', 'nombre' => 'Pollo a la Brasa (1/2)', 'precio' => 32.00, 'precio_delivery' => 34.00],
            ['categoria_id' => $pollosId, 'codigo' => 'POL003', 'nombre' => 'Pollo a la Brasa (entero)', 'precio' => 58.00, 'precio_delivery' => 60.00],
            // Menús
            ['categoria_id' => $menusId, 'codigo' => 'MEN001', 'nombre' => 'Menú Ejecutivo', 'precio' => 15.00, 'precio_delivery' => 17.00],
            ['categoria_id' => $menusId, 'codigo' => 'MEN002', 'nombre' => 'Menú Especial', 'precio' => 22.00, 'precio_delivery' => 24.00],
            // Postres
            ['categoria_id' => $postresId, 'codigo' => 'POS001', 'nombre' => 'Helado 2 bolas', 'precio' => 8.00, 'precio_delivery' => 9.00],
            ['categoria_id' => $postresId, 'codigo' => 'POS002', 'nombre' => 'Torta de Chocolate', 'precio' => 12.00, 'precio_delivery' => 13.00],
        ];

        foreach ($productos as $p) {
            Producto::firstOrCreate(
                ['codigo' => $p['codigo']],
                array_merge($p, ['disponible' => true, 'activo' => true, 'stock' => 100, 'unidad' => 'unidad'])
            );
        }
    }
}
