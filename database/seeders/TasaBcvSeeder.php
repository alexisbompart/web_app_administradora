<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo\TasaBcv;
use Carbon\Carbon;

class TasaBcvSeeder extends Seeder
{
    /**
     * Seed the cond_tasas_bcv table with recent exchange rates.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $tasas = [
            [
                'moneda'    => 'USD',
                'tasa'      => 36.5180,
                'fecha'     => $today->copy()->subDays(4)->toDateString(),
                'fuente'    => 'BCV',
            ],
            [
                'moneda'    => 'USD',
                'tasa'      => 36.5250,
                'fecha'     => $today->copy()->subDays(3)->toDateString(),
                'fuente'    => 'BCV',
            ],
            [
                'moneda'    => 'USD',
                'tasa'      => 36.4975,
                'fecha'     => $today->copy()->subDays(2)->toDateString(),
                'fuente'    => 'BCV',
            ],
            [
                'moneda'    => 'USD',
                'tasa'      => 36.5100,
                'fecha'     => $today->copy()->subDays(1)->toDateString(),
                'fuente'    => 'BCV',
            ],
            [
                'moneda'    => 'USD',
                'tasa'      => 36.5320,
                'fecha'     => $today->toDateString(),
                'fuente'    => 'BCV',
            ],
        ];

        foreach ($tasas as $tasa) {
            TasaBcv::updateOrCreate(
                [
                    'moneda' => $tasa['moneda'],
                    'fecha'  => $tasa['fecha'],
                ],
                $tasa
            );
        }
    }
}
