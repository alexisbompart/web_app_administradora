<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Condominio\Propietario;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;

class PropietarioSeeder extends Seeder
{
    public function run(): void
    {
        $propietarios = [
            ['nombres' => 'Carlos',   'apellidos' => 'González',  'cedula' => 'V-12345678', 'telefono' => '0212-555-3001', 'celular' => '0414-555-3001', 'email' => 'carlos.gonzalez@email.com'],
            ['nombres' => 'María',    'apellidos' => 'Rodríguez', 'cedula' => 'V-23456789', 'telefono' => '0212-555-3002', 'celular' => '0424-555-3002', 'email' => 'maria.rodriguez@email.com'],
            ['nombres' => 'José',     'apellidos' => 'Pérez',     'cedula' => 'V-34567890', 'telefono' => '0212-555-3003', 'celular' => '0412-555-3003', 'email' => 'jose.perez@email.com'],
            ['nombres' => 'Ana',      'apellidos' => 'López',     'cedula' => 'V-45678901', 'telefono' => '0212-555-3004', 'celular' => '0416-555-3004', 'email' => 'ana.lopez@email.com'],
            ['nombres' => 'Luis',     'apellidos' => 'Martínez',  'cedula' => 'V-56789012', 'telefono' => '0212-555-3005', 'celular' => '0414-555-3005', 'email' => 'luis.martinez@email.com'],
            ['nombres' => 'Laura',    'apellidos' => 'García',    'cedula' => 'V-67890123', 'telefono' => '0212-555-3006', 'celular' => '0424-555-3006', 'email' => 'laura.garcia@email.com'],
            ['nombres' => 'Pedro',    'apellidos' => 'Hernández', 'cedula' => 'V-78901234', 'telefono' => '0212-555-3007', 'celular' => '0412-555-3007', 'email' => 'pedro.hernandez@email.com'],
            ['nombres' => 'Carmen',   'apellidos' => 'Díaz',      'cedula' => 'V-89012345', 'telefono' => '0212-555-3008', 'celular' => '0416-555-3008', 'email' => 'carmen.diaz@email.com'],
            ['nombres' => 'Rafael',   'apellidos' => 'Morales',   'cedula' => 'V-90123456', 'telefono' => '0212-555-3009', 'celular' => '0414-555-3009', 'email' => 'rafael.morales@email.com'],
            ['nombres' => 'Isabel',   'apellidos' => 'Torres',    'cedula' => 'V-10234567', 'telefono' => '0212-555-3010', 'celular' => '0424-555-3010', 'email' => 'isabel.torres@email.com'],
            ['nombres' => 'Miguel',   'apellidos' => 'Ramos',     'cedula' => 'V-11234567', 'telefono' => '0212-555-3011', 'celular' => '0412-555-3011', 'email' => 'miguel.ramos@email.com'],
            ['nombres' => 'Adriana',  'apellidos' => 'Castro',    'cedula' => 'V-12234567', 'telefono' => '0212-555-3012', 'celular' => '0416-555-3012', 'email' => 'adriana.castro@email.com'],
            ['nombres' => 'Fernando', 'apellidos' => 'Méndez',    'cedula' => 'V-13234567', 'telefono' => '0212-555-3013', 'celular' => '0414-555-3013', 'email' => 'fernando.mendez@email.com'],
            ['nombres' => 'Luisa',    'apellidos' => 'Vargas',    'cedula' => 'V-14234567', 'telefono' => '0212-555-3014', 'celular' => '0424-555-3014', 'email' => 'luisa.vargas@email.com'],
            ['nombres' => 'Roberto',  'apellidos' => 'Flores',    'cedula' => 'V-15234567', 'telefono' => '0212-555-3015', 'celular' => '0412-555-3015', 'email' => 'roberto.flores@email.com'],
        ];

        $createdPropietarios = [];

        foreach ($propietarios as $data) {
            // Crear usuario para el propietario
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nombres'] . ' ' . $data['apellidos'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'cedula' => $data['cedula'],
                    'telefono' => $data['celular'],
                    'activo' => true,
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->hasRole('cliente-propietario')) {
                $user->assignRole('cliente-propietario');
            }

            // Crear propietario vinculado al usuario
            $createdPropietarios[] = Propietario::updateOrCreate(
                ['cedula' => $data['cedula']],
                array_merge($data, [
                    'direccion' => 'Caracas, Venezuela',
                    'estatus'   => true,
                    'user_id'   => $user->id,
                ])
            );
        }

        // Assign first 10 owners to apartments in TORRE-A
        $edificio = Edificio::where('cod_edif', 'TORRE-A')->first();

        if (!$edificio) {
            $this->command->error('Edificio TORRE-A not found. Run EdificioSeeder and ApartamentoSeeder first.');
            return;
        }

        $apartamentos = Apartamento::where('edificio_id', $edificio->id)
            ->orderBy('num_apto')
            ->take(10)
            ->get();

        for ($i = 0; $i < 10 && $i < count($apartamentos); $i++) {
            $propietario = $createdPropietarios[$i];
            $apartamento = $apartamentos[$i];

            // Avoid duplicates
            $exists = DB::table('propietario_apartamento')
                ->where('propietario_id', $propietario->id)
                ->where('apartamento_id', $apartamento->id)
                ->exists();

            if (!$exists) {
                DB::table('propietario_apartamento')->insert([
                    'propietario_id'    => $propietario->id,
                    'apartamento_id'    => $apartamento->id,
                    'propietario_actual' => true,
                    'fecha_desde'       => '2024-01-01',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }
    }
}
