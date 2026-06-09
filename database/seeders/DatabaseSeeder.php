<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        User::factory()->create([
            'name' => 'Admin Papelería',
            'email' => 'admin@papeleria.com',
            'password' => bcrypt('admin123'),
        ]);

        // Ejecutar los seeders en orden de dependencias
        $this->call([
            CategoriasSeeder::class,
            MetodosPagoSeeder::class,
            ProveedoresSeeder::class,
            ClientesSeeder::class,
            ProductosSeeder::class,
        ]);
    }
}

