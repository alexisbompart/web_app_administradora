<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Financiero\Banco;

class BancoSeeder extends Seeder
{
    /**
     * Seed the bancos table with major Venezuelan banks.
     */
    public function run(): void
    {
        $bancos = [
            ['cod_banco' => '0102', 'nombre' => 'Banco de Venezuela',          'iniciales' => 'BDV'],
            ['cod_banco' => '0104', 'nombre' => 'Venezolano de Crédito',       'iniciales' => 'BVC'],
            ['cod_banco' => '0105', 'nombre' => 'Mercantil',                   'iniciales' => 'BM'],
            ['cod_banco' => '0108', 'nombre' => 'Provincial',                  'iniciales' => 'BP'],
            ['cod_banco' => '0114', 'nombre' => 'Bancaribe',                   'iniciales' => 'BC'],
            ['cod_banco' => '0115', 'nombre' => 'Exterior',                    'iniciales' => 'BE'],
            ['cod_banco' => '0128', 'nombre' => 'Banco Caroní',               'iniciales' => 'BCNI'],
            ['cod_banco' => '0134', 'nombre' => 'Banesco',                     'iniciales' => 'BAN'],
            ['cod_banco' => '0137', 'nombre' => 'Sofitasa',                    'iniciales' => 'SOF'],
            ['cod_banco' => '0138', 'nombre' => 'Banco Plaza',                 'iniciales' => 'BPL'],
            ['cod_banco' => '0151', 'nombre' => 'BFC Banco Fondo Común',       'iniciales' => 'BFC'],
            ['cod_banco' => '0156', 'nombre' => '100% Banco',                  'iniciales' => '100B'],
            ['cod_banco' => '0163', 'nombre' => 'Banco del Tesoro',            'iniciales' => 'BT'],
            ['cod_banco' => '0166', 'nombre' => 'Bangente',                    'iniciales' => 'BNG'],
            ['cod_banco' => '0168', 'nombre' => 'Bancrecer',                   'iniciales' => 'BCR'],
            ['cod_banco' => '0169', 'nombre' => 'Mi Banco',                    'iniciales' => 'MB'],
            ['cod_banco' => '0171', 'nombre' => 'Activo',                      'iniciales' => 'BA'],
            ['cod_banco' => '0172', 'nombre' => 'Bancamiga',                   'iniciales' => 'BCA'],
            ['cod_banco' => '0174', 'nombre' => 'Banplus',                     'iniciales' => 'BPL2'],
            ['cod_banco' => '0175', 'nombre' => 'Bicentenario',                'iniciales' => 'BIC'],
            ['cod_banco' => '0177', 'nombre' => 'Banfanb',                     'iniciales' => 'BFNB'],
            ['cod_banco' => '0191', 'nombre' => 'BNC Nacional de Crédito',     'iniciales' => 'BNC'],
        ];

        foreach ($bancos as $banco) {
            Banco::updateOrCreate(
                ['cod_banco' => $banco['cod_banco']],
                $banco
            );
        }
    }
}
