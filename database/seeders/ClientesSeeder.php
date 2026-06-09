<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Público General',
                'telefono' => '000000000',
                'email' => 'general@papeleria.com',
                'total_compras' => 0.00,
                'ultima_compra' => null
            ],
            [
                'nombre' => 'Erick Huamán',
                'telefono' => '999888777',
                'email' => 'erick@gmail.com',
                'total_compras' => 150.50,
                'ultima_compra' => '2026-06-04'
            ],
            [
                'nombre' => 'Ana Torres',
                'telefono' => '955444333',
                'email' => 'ana.torres@outlook.com',
                'total_compras' => 45.20,
                'ultima_compra' => '2026-06-05'
            ],
        ];

        foreach ($clientes as $c) {
            Cliente::create($c);
        }
    }
}
