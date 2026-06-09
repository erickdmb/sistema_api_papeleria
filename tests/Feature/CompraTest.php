<?php

namespace Tests\Feature;

use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    private $proveedor;
    private $producto;

    protected function setUp(): void
    {
        parent::setUp();

        $categoria = Categoria::create([
            'nombre' => 'Lapiceros',
            'descripcion' => 'Lapiceros varios'
        ]);

        $this->proveedor = Proveedor::create([
            'razon_social' => 'Proveedor de Prueba S.A.',
            'contacto_nombre' => 'Luis Castillo',
            'contacto_telefono' => '987654321',
            'contacto_email' => 'luis@proveedortest.com'
        ]);

        $this->producto = Producto::create([
            'nombre' => 'Lapicero Test Azul',
            'categoria_id' => $categoria->id_categoria,
            'precio_compra' => 0.80,
            'precio_venta' => 1.50,
            'stock_actual' => 50,
            'stock_minimo' => 10,
            'sku' => 'TEST-LAPI-01',
            'activo' => true
        ]);
    }

    public function test_debe_registrar_compra_exitosamente_y_aumentar_stock()
    {
        $payload = [
            'proveedor_id' => $this->proveedor->id_proveedor,
            'ruc' => '10456789012',
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 30,
                    'precio_unitario' => 0.90 // Nuevo costo
                ]
            ]
        ];

        $response = $this->postJson('/api/compras', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('estado', 'REGISTRADA');
        $response->assertJsonPath('total', '27.00'); // 30 * 0.90

        // Verificar stock incrementado
        $this->producto->refresh();
        $this->assertEquals(80, $this->producto->stock_actual);

        // Verificar costo de compra actualizado en el catálogo
        $this->assertEquals('0.90', $this->producto->precio_compra);
    }

    public function test_debe_anular_compra_y_disminuir_stock()
    {
        // 1. Registrar compra de 20 lapiceros
        $payload = [
            'proveedor_id' => $this->proveedor->id_proveedor,
            'ruc' => '10456789012',
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 20,
                    'precio_unitario' => 0.85
                ]
            ]
        ];

        $resCompra = $this->postJson('/api/compras', $payload);
        $compraId = $resCompra->json('id_compra');

        $this->producto->refresh();
        $this->assertEquals(70, $this->producto->stock_actual); // 50 + 20

        // 2. Anular la compra
        $response = $this->postJson("/api/compras/{$compraId}/anular");

        $response->assertStatus(200);
        $response->assertJsonPath('compra.estado', 'ANULADA');

        // Verificar stock retirado
        $this->producto->refresh();
        $this->assertEquals(50, $this->producto->stock_actual);
    }

    public function test_no_debe_anular_compra_si_el_stock_actual_es_insuficiente()
    {
        // 1. Registrar compra de 30 lapiceros
        $payload = [
            'proveedor_id' => $this->proveedor->id_proveedor,
            'ruc' => '10456789012',
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 30,
                    'precio_unitario' => 0.85
                ]
            ]
        ];

        $resCompra = $this->postJson('/api/compras', $payload);
        $compraId = $resCompra->json('id_compra');

        $this->producto->refresh();
        $this->assertEquals(80, $this->producto->stock_actual); // 50 + 30

        // 2. Simular venta o retiro de stock para dejar stock por debajo de la compra realizada (ej. bajar a 20 unidades)
        $this->producto->stock_actual = 20;
        $this->producto->save();

        // 3. Intentar anular la compra (quiere retirar 30 unidades, pero solo quedan 20 en stock físico)
        $response = $this->postJson("/api/compras/{$compraId}/anular");

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'error']);
        $response->assertJsonFragment([
            'error' => "No se puede anular la compra. El stock actual de 'Lapicero Test Azul' (20) es menor a la cantidad a retirar (30)."
        ]);

        // Verificar que el stock sigue en 20
        $this->producto->refresh();
        $this->assertEquals(20, $this->producto->stock_actual);
    }
}
