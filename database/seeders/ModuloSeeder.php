<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo\Modulo;

class ModuloSeeder extends Seeder
{
    /**
     * Seed the modulos table with system modules.
     */
    public function run(): void
    {
        $modulos = [
            [
                'nombre' => 'Personal',
                'icono'  => 'fa-users',
                'ruta'   => '/personal',
                'orden'  => 1,
            ],
            [
                'nombre' => 'Proveedores',
                'icono'  => 'fa-truck',
                'ruta'   => '/proveedores',
                'orden'  => 2,
            ],
            [
                'nombre' => 'Fondos',
                'icono'  => 'fa-wallet',
                'ruta'   => '/fondos',
                'orden'  => 3,
            ],
            [
                'nombre' => 'Cobranza',
                'icono'  => 'fa-file-invoice-dollar',
                'ruta'   => '/cobranza',
                'orden'  => 4,
            ],
            [
                'nombre' => 'Pago Integral',
                'icono'  => 'fa-money-check-alt',
                'ruta'   => '/pago-integral',
                'orden'  => 5,
            ],
            [
                'nombre' => 'CajaMatic',
                'icono'  => 'fa-cash-register',
                'ruta'   => '/cajamatic',
                'orden'  => 6,
            ],
            [
                'nombre' => 'Atención al Cliente',
                'icono'  => 'fa-headset',
                'ruta'   => '/atencion-cliente',
                'orden'  => 7,
            ],
            [
                'nombre' => 'Informes a la Comunidad',
                'icono'  => 'fa-chart-bar',
                'ruta'   => '/informes-comunidad',
                'orden'  => 8,
            ],
        ];

        foreach ($modulos as $modulo) {
            Modulo::updateOrCreate(
                ['nombre' => $modulo['nombre']],
                $modulo
            );
        }
    }
}
