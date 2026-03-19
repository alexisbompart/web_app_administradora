<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;
use App\Models\Financiero\Fondo;

class FondoSeeder extends Seeder
{
    /**
     * Seed the fondos table with demo funds.
     */
    public function run(): void
    {
        $compania = Compania::where('cod_compania', 'ADM001')->first();

        if (!$compania) {
            $this->command->error('Compania ADM001 not found. Run CompaniaSeeder first.');
            return;
        }

        $fondos = [
            [
                'compania_id'  => $compania->id,
                'nombre'       => 'Fondo de Contingencias',
                'tipo'         => 'contingencias',
                'saldo_actual' => 25000.00,
                'descripcion'  => 'Fondo destinado a cubrir gastos imprevistos y emergencias del edificio.',
                'activo'       => true,
            ],
            [
                'compania_id'  => $compania->id,
                'nombre'       => 'Fondo de Prestaciones',
                'tipo'         => 'prestaciones',
                'saldo_actual' => 50000.00,
                'descripcion'  => 'Fondo para prestaciones sociales del personal.',
                'activo'       => true,
            ],
            [
                'compania_id'  => $compania->id,
                'nombre'       => 'Fondo de Reserva',
                'tipo'         => 'reserva',
                'saldo_actual' => 15000.00,
                'descripcion'  => 'Fondo de reserva legal del condominio.',
                'activo'       => true,
            ],
            [
                'compania_id'  => $compania->id,
                'nombre'       => 'Fondo Especial',
                'tipo'         => 'especial',
                'saldo_actual' => 5000.00,
                'descripcion'  => 'Fondo especial para proyectos y mejoras.',
                'activo'       => true,
            ],
        ];

        foreach ($fondos as $fondo) {
            Fondo::updateOrCreate(
                [
                    'compania_id' => $fondo['compania_id'],
                    'nombre'      => $fondo['nombre'],
                ],
                $fondo
            );
        }
    }
}
