<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Cuadernos', 'descripcion' => 'Cuadernos de todo tipo: cuadriculados, rayados, triple renglón, etc.'],
            ['nombre' => 'Lapiceros', 'descripcion' => 'Lapiceros, bolígrafos, plumas y correctores.'],
            ['nombre' => 'Fólderes', 'descripcion' => 'Fólderes de plástico, cartón, manila y archivadores.'],
            ['nombre' => 'Papel Lustre', 'descripcion' => 'Pliegos de papel lustre de diferentes colores.'],
            ['nombre' => 'Útiles Generales', 'descripcion' => 'Reglas, tajadores, borradores, gomas, tijeras, etc.'],
        ];

        foreach ($categorias as $c) {
            Categoria::create($c);
        }
    }
}
