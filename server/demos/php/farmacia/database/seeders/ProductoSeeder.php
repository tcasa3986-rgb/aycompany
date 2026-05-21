<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Categoria::pluck('id', 'nombre');
        $provs = Proveedor::pluck('id')->all();

        $productos = [
            ['codigo' => 'PARAC500', 'nombre' => 'Paracetamol 500 mg',      'principio_activo' => 'Paracetamol', 'presentacion' => 'Tableta', 'concentracion' => '500 mg', 'tipo' => 'generico',  'cat' => 'Analgésicos',     'pc' => 0.10, 'pv' => 0.30, 'stock' => 480],
            ['codigo' => 'IBU400',   'nombre' => 'Ibuprofeno 400 mg',       'principio_activo' => 'Ibuprofeno',  'presentacion' => 'Tableta', 'concentracion' => '400 mg', 'tipo' => 'generico',  'cat' => 'Antiinflamatorios','pc' => 0.15, 'pv' => 0.45, 'stock' => 320],
            ['codigo' => 'AMOX500',  'nombre' => 'Amoxicilina 500 mg',      'principio_activo' => 'Amoxicilina', 'presentacion' => 'Cápsula', 'concentracion' => '500 mg', 'tipo' => 'generico',  'cat' => 'Antibióticos',    'pc' => 0.30, 'pv' => 0.80, 'stock' => 210, 'requiere_receta' => true],
            ['codigo' => 'LORAT10',  'nombre' => 'Loratadina 10 mg',        'principio_activo' => 'Loratadina',  'presentacion' => 'Tableta', 'concentracion' => '10 mg',  'tipo' => 'generico',  'cat' => 'Antialérgicos',   'pc' => 0.20, 'pv' => 0.60, 'stock' => 150],
            ['codigo' => 'VITC1G',   'nombre' => 'Vitamina C 1 g',          'principio_activo' => 'Ácido ascórbico','presentacion' => 'Tableta efervescente','concentracion' => '1 g','tipo' => 'generico','cat' => 'Vitaminas','pc' => 0.40, 'pv' => 1.20, 'stock' => 90],
            ['codigo' => 'PANAD500', 'nombre' => 'Panadol 500 mg',          'principio_activo' => 'Paracetamol', 'presentacion' => 'Tableta', 'concentracion' => '500 mg', 'tipo' => 'marca',     'cat' => 'Analgésicos',     'pc' => 0.50, 'pv' => 1.20, 'stock' => 60],
            ['codigo' => 'CETIR10',  'nombre' => 'Cetirizina 10 mg',        'principio_activo' => 'Cetirizina',  'presentacion' => 'Tableta', 'concentracion' => '10 mg',  'tipo' => 'generico',  'cat' => 'Antialérgicos',   'pc' => 0.20, 'pv' => 0.55, 'stock' => 4,  'stock_minimo' => 10],
            ['codigo' => 'PROT100',  'nombre' => 'Protector solar FPS 50',  'principio_activo' => null,           'presentacion' => 'Crema',  'concentracion' => '100 ml', 'tipo' => 'cosmetico', 'cat' => 'Cosméticos',      'pc' => 18.00,'pv' => 32.00,'stock' => 22],
            ['codigo' => 'JERIN5',   'nombre' => 'Jeringa descartable 5 ml','principio_activo' => null,           'presentacion' => 'Unidad', 'concentracion' => '5 ml',   'tipo' => 'insumo',    'cat' => 'Insumos médicos', 'pc' => 0.15, 'pv' => 0.50, 'stock' => 800],
            ['codigo' => 'PANIB',    'nombre' => 'Pañal infantil M x 30',   'principio_activo' => null,           'presentacion' => 'Paquete','concentracion' => '30 unid','tipo' => 'cosmetico', 'cat' => 'Cuidado infantil','pc' => 22.00,'pv' => 35.00,'stock' => 18],
        ];

        foreach ($productos as $p) {
            $producto = Producto::updateOrCreate(['codigo' => $p['codigo']], [
                'nombre'           => $p['nombre'],
                'principio_activo' => $p['principio_activo'] ?? null,
                'presentacion'     => $p['presentacion'] ?? null,
                'concentracion'    => $p['concentracion'] ?? null,
                'categoria_id'     => $cats[$p['cat']] ?? null,
                'proveedor_id'     => $provs[array_rand($provs)] ?? null,
                'tipo'             => $p['tipo'] ?? 'generico',
                'precio_compra'    => $p['pc'],
                'precio_venta'     => $p['pv'],
                'stock'            => $p['stock'],
                'stock_minimo'     => $p['stock_minimo'] ?? 5,
                'stock_maximo'     => 800,
                'requiere_receta'  => $p['requiere_receta'] ?? false,
                'ubicacion'        => 'Estante ' . chr(64 + rand(1, 6)) . '-' . rand(1, 9),
                'activo'           => true,
            ]);

            Lote::updateOrCreate(
                ['producto_id' => $producto->id, 'numero_lote' => 'L' . now()->format('Y') . '-' . str_pad((string) $producto->id, 3, '0', STR_PAD_LEFT)],
                [
                    'fecha_vencimiento' => now()->addMonths(rand(2, 24)),
                    'cantidad'          => $p['stock'],
                ]
            );
        }
    }
}
