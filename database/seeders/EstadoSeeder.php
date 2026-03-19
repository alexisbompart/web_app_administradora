<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Seed the estados table with all 24 Venezuelan states.
     */
    public function run(): void
    {
        $estados = [
            'Amazonas',
            'Anzoátegui',
            'Apure',
            'Aragua',
            'Barinas',
            'Bolívar',
            'Carabobo',
            'Cojedes',
            'Delta Amacuro',
            'Distrito Capital',
            'Falcón',
            'Guárico',
            'Lara',
            'Mérida',
            'Miranda',
            'Monagas',
            'Nueva Esparta',
            'Portuguesa',
            'Sucre',
            'Táchira',
            'Trujillo',
            'Vargas',
            'Yaracuy',
            'Zulia',
        ];

        foreach ($estados as $estado) {
            Estado::updateOrCreate(
                ['nombre' => $estado],
                ['nombre' => $estado]
            );
        }
    }
}
