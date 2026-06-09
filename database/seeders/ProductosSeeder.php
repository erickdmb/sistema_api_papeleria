<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Cuadernos (categoria_id: 1)
            [
                'nombre' => 'Cuaderno Stanford Cuadriculado A4 100 Hojas',
                'descripcion' => 'Cuaderno escolar cuadriculado de 100 hojas, tapa dura anillado.',
                'categoria_id' => 1,
                'precio_compra' => 4.50,
                'precio_venta' => 7.00,
                'stock_actual' => 50,
                'stock_minimo' => 10,
                'sku' => 'PROD-CUAD-001',
                'marca' => 'Stanford',
                'color' => 'Varios',
                'tamaño' => 'A4',
                'activo' => true
            ],
            [
                'nombre' => 'Cuaderno Stanford Rayado A4 100 Hojas',
                'descripcion' => 'Cuaderno escolar rayado de 100 hojas, tapa dura anillado.',
                'categoria_id' => 1,
                'precio_compra' => 4.50,
                'precio_venta' => 7.00,
                'stock_actual' => 40,
                'stock_minimo' => 10,
                'sku' => 'PROD-CUAD-002',
                'marca' => 'Stanford',
                'color' => 'Varios',
                'tamaño' => 'A4',
                'activo' => true
            ],
            // Lapiceros (categoria_id: 2)
            [
                'nombre' => 'Lapicero Pilot G2 Gel Azul 0.7mm',
                'descripcion' => 'Bolígrafo de tinta gel Pilot G2 retráctil color azul.',
                'categoria_id' => 2,
                'precio_compra' => 3.50,
                'precio_venta' => 5.50,
                'stock_actual' => 100,
                'stock_minimo' => 15,
                'sku' => 'PROD-LAPI-001',
                'marca' => 'Pilot',
                'color' => 'Azul',
                'tamaño' => '0.7mm',
                'activo' => true
            ],
            [
                'nombre' => 'Lapicero Faber-Castell Trilux 032 Negro',
                'descripcion' => 'Bolígrafo clásico Faber-Castell Trilux color negro.',
                'categoria_id' => 2,
                'precio_compra' => 0.50,
                'precio_venta' => 1.00,
                'stock_actual' => 200,
                'stock_minimo' => 30,
                'sku' => 'PROD-LAPI-002',
                'marca' => 'Faber-Castell',
                'color' => 'Negro',
                'tamaño' => 'Mediano',
                'activo' => true
            ],
            [
                'nombre' => 'Lapicero Faber-Castell Trilux 032 Rojo',
                'descripcion' => 'Bolígrafo clásico Faber-Castell Trilux color rojo.',
                'categoria_id' => 2,
                'precio_compra' => 0.50,
                'precio_venta' => 1.00,
                'stock_actual' => 150,
                'stock_minimo' => 20,
                'sku' => 'PROD-LAPI-003',
                'marca' => 'Faber-Castell',
                'color' => 'Rojo',
                'tamaño' => 'Mediano',
                'activo' => true
            ],
            // Fólderes (categoria_id: 3)
            [
                'nombre' => 'Fólder Manila A4',
                'descripcion' => 'Carpeta de cartón manila tamaño A4.',
                'categoria_id' => 3,
                'precio_compra' => 0.20,
                'precio_venta' => 0.60,
                'stock_actual' => 500,
                'stock_minimo' => 50,
                'sku' => 'PROD-FOLD-001',
                'marca' => 'Genérica',
                'color' => 'Amarillo manila',
                'tamaño' => 'A4',
                'activo' => true
            ],
            [
                'nombre' => 'Fólder Archivador de Palanca Lomo Ancho Oficio',
                'descripcion' => 'Archivador pesado de palanca con lomo ancho para oficina.',
                'categoria_id' => 3,
                'precio_compra' => 5.20,
                'precio_venta' => 8.50,
                'stock_actual' => 30,
                'stock_minimo' => 5,
                'sku' => 'PROD-FOLD-002',
                'marca' => 'Artesco',
                'color' => 'Negro',
                'tamaño' => 'Oficio',
                'activo' => true
            ],
            // Papel Lustre (categoria_id: 4)
            [
                'nombre' => 'Papel Lustre Pliego Rojo',
                'descripcion' => 'Pliego de papel lustre color rojo brillante.',
                'categoria_id' => 4,
                'precio_compra' => 0.40,
                'precio_venta' => 0.80,
                'stock_actual' => 80,
                'stock_minimo' => 15,
                'sku' => 'PROD-LUST-001',
                'marca' => 'Genérica',
                'color' => 'Rojo',
                'tamaño' => 'Pliego',
                'activo' => true
            ],
            [
                'nombre' => 'Papel Lustre Pliego Azul',
                'descripcion' => 'Pliego de papel lustre color azul brillante.',
                'categoria_id' => 4,
                'precio_compra' => 0.40,
                'precio_venta' => 0.80,
                'stock_actual' => 4,
                'stock_minimo' => 15,
                'sku' => 'PROD-LUST-002',
                'marca' => 'Genérica',
                'color' => 'Azul',
                'tamaño' => 'Pliego',
                'activo' => true
            ],
            [
                'nombre' => 'Papel Lustre Pliego Amarillo',
                'descripcion' => 'Pliego de papel lustre color amarillo brillante.',
                'categoria_id' => 4,
                'precio_compra' => 0.40,
                'precio_venta' => 0.80,
                'stock_actual' => 75,
                'stock_minimo' => 15,
                'sku' => 'PROD-LUST-003',
                'marca' => 'Genérica',
                'color' => 'Amarillo',
                'tamaño' => 'Pliego',
                'activo' => true
            ],
            // Útiles Generales (categoria_id: 5)
            [
                'nombre' => 'Regla de Plástico Faber-Castell 30cm',
                'descripcion' => 'Regla de plástico transparente biselada de 30 cm.',
                'categoria_id' => 5,
                'precio_compra' => 1.20,
                'precio_venta' => 2.00,
                'stock_actual' => 60,
                'stock_minimo' => 10,
                'sku' => 'PROD-UTIL-001',
                'marca' => 'Faber-Castell',
                'color' => 'Transparente',
                'tamaño' => '30cm',
                'activo' => true
            ],
        ];

        foreach ($productos as $p) {
            Producto::create($p);
        }
    }
}
