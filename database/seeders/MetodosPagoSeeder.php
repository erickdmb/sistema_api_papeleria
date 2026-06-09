<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Seeder;

class MetodosPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo de curso legal.'],
            ['nombre' => 'Tarjeta', 'descripcion' => 'Tarjeta de crédito o débito (Visa, Mastercard, etc.).'],
            ['nombre' => 'Yape', 'descripcion' => 'Billetera digital Yape.'],
            ['nombre' => 'Plin', 'descripcion' => 'Billetera digital Plin.'],
            ['nombre' => 'Transferencia', 'descripcion' => 'Transferencia bancaria directa.'],
        ];

        foreach ($metodos as $m) {
            MetodoPago::create($m);
        }
    }
}
