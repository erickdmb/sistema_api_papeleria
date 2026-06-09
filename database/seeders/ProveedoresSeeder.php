<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedoresSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'razon_social' => 'Distribuidora Navarrete S.A.',
                'contacto_nombre' => 'Juan Pérez',
                'contacto_telefono' => '987654321',
                'contacto_email' => 'ventas@navarrete.com.pe',
                'direccion' => 'Av. Nicolás de Piérola 456, Lima',
                'activo' => true
            ],
            [
                'razon_social' => 'Faber-Castell Peruana S.A.',
                'contacto_nombre' => 'María Gómez',
                'contacto_telefono' => '912345678',
                'contacto_email' => 'corporativo@faber-castell.pe',
                'direccion' => 'Av. La Molina 1234, Lima',
                'activo' => true
            ],
            [
                'razon_social' => 'Artisco S.A.C.',
                'contacto_nombre' => 'Carlos López',
                'contacto_telefono' => '934567890',
                'contacto_email' => 'ventas@artisco.com.pe',
                'direccion' => 'Calle Los Claveles 789, Lince, Lima',
                'activo' => true
            ],
        ];

        foreach ($proveedores as $p) {
            Proveedor::create($p);
        }
    }
}
