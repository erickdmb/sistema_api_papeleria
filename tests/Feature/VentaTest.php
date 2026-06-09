<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    private $cliente;
    private $metodoPago;
    private $producto;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos base para pruebas
        $categoria = Categoria::create([
            'nombre' => 'Cuadernos',
            'descripcion' => 'Cuadernos varios'
        ]);

        $this->cliente = Cliente::create([
            'nombre' => 'Cliente Test',
            'telefono' => '999888777',
            'email' => 'test@client.com'
        ]);

        $this->metodoPago = MetodoPago::create([
            'nombre' => 'Efectivo',
            'descripcion' => 'Pago en efectivo'
        ]);

        $this->producto = Producto::create([
            'nombre' => 'Cuaderno Test 100 Hojas',
            'categoria_id' => $categoria->id_categoria,
            'precio_compra' => 4.00,
            'precio_venta' => 7.00,
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'sku' => 'TEST-CUAD-01',
            'activo' => true
        ]);
    }

    public function test_debe_registrar_venta_exitosamente_y_descontar_stock()
    {
        $payload = [
            'id_cliente' => $this->cliente->id_cliente,
            'id_metodo_pago' => $this->metodoPago->id_metodo_pago,
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 4
                ]
            ]
        ];

        $response = $this->postJson('/api/ventas', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('estado', 'COMPLETADA');
        $response->assertJsonPath('total', '28.00');

        // Verificar stock descontado
        $this->producto->refresh();
        $this->assertEquals(6, $this->producto->stock_actual);

        // Verificar total del cliente actualizado
        $this->cliente->refresh();
        $this->assertEquals('28.00', $this->cliente->total_compras);
        $this->assertNotNull($this->cliente->ultima_compra);
    }

    public function test_debe_rechazar_venta_si_no_hay_suficiente_stock()
    {
        $payload = [
            'id_cliente' => $this->cliente->id_cliente,
            'id_metodo_pago' => $this->metodoPago->id_metodo_pago,
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 12 // Más de los 10 disponibles
                ]
            ]
        ];

        $response = $this->postJson('/api/ventas', $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'error']);
        $response->assertJsonFragment([
            'error' => "Stock insuficiente para el producto: 'Cuaderno Test 100 Hojas'. Stock disponible: 10."
        ]);

        // Verificar que el stock se mantiene en 10
        $this->producto->refresh();
        $this->assertEquals(10, $this->producto->stock_actual);
    }

    public function test_debe_anular_venta_y_restablecer_stock()
    {
        // 1. Crear una venta
        $payload = [
            'id_cliente' => $this->cliente->id_cliente,
            'id_metodo_pago' => $this->metodoPago->id_metodo_pago,
            'detalles' => [
                [
                    'id_producto' => $this->producto->id_producto,
                    'cantidad' => 3
                ]
            ]
        ];

        $resVenta = $this->postJson('/api/ventas', $payload);
        $ventaId = $resVenta->json('id_venta');

        $this->producto->refresh();
        $this->assertEquals(7, $this->producto->stock_actual);

        // 2. Anular la venta
        $response = $this->postJson("/api/ventas/{$ventaId}/anular");

        $response->assertStatus(200);
        $response->assertJsonPath('venta.estado', 'ANULADA');

        // Verificar stock devuelto
        $this->producto->refresh();
        $this->assertEquals(10, $this->producto->stock_actual);

        // Verificar total del cliente recalculado
        $this->cliente->refresh();
        $this->assertEquals('0.00', $this->cliente->total_compras);
        $this->assertNull($this->cliente->ultima_compra);
    }
}
