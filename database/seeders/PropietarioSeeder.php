<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Condominio\Propietario;
use App\Models\Condominio\Apartamento;

class PropietarioSeeder extends Seeder
{
    // Apartamentos reales del edificio 116 (CONJ. RESD. LOMA DE LOS MANGOS EDIFICIO 1)
    // con datos de deudas, pagos y facturas ya exportados al seeder.
    // cod_pint -> identificador estable que no cambia al reiniciar la BD.
    private array $asignaciones = [
        ['email' => 'cliente@test.com',             'nombres' => 'Cliente',   'apellidos' => 'Demo',       'cedula' => 'V-00000001', 'cod_pint' => '03678'], // 12-B
        ['email' => 'carlos.gonzalez@email.com',    'nombres' => 'Carlos',    'apellidos' => 'González',   'cedula' => 'V-12345678', 'cod_pint' => '03678'], // 12-B
        ['email' => 'maria.rodriguez@email.com',    'nombres' => 'María',     'apellidos' => 'Rodríguez',  'cedula' => 'V-23456789', 'cod_pint' => '03684'], // 13-B
        ['email' => 'jose.perez@email.com',         'nombres' => 'José',      'apellidos' => 'Pérez',      'cedula' => 'V-34567890', 'cod_pint' => '03689'], // 14-A
        ['email' => 'ana.lopez@email.com',          'nombres' => 'Ana',       'apellidos' => 'López',      'cedula' => 'V-45678901', 'cod_pint' => '03677'], // 12-A
        ['email' => 'luis.martinez@email.com',      'nombres' => 'Luis',      'apellidos' => 'Martínez',   'cedula' => 'V-56789012', 'cod_pint' => '03671'], // 11-A
        ['email' => 'laura.garcia@email.com',       'nombres' => 'Laura',     'apellidos' => 'García',     'cedula' => 'V-67890123', 'cod_pint' => '03683'], // 13-A
        ['email' => 'pedro.hernandez@email.com',    'nombres' => 'Pedro',     'apellidos' => 'Hernández',  'cedula' => 'V-78901234', 'cod_pint' => '03672'], // 11-B
        ['email' => 'carmen.diaz@email.com',        'nombres' => 'Carmen',    'apellidos' => 'Díaz',       'cedula' => 'V-89012345', 'cod_pint' => '03690'], // 14-B
        ['email' => 'rafael.morales@email.com',     'nombres' => 'Rafael',    'apellidos' => 'Morales',    'cedula' => 'V-90123456', 'cod_pint' => '03679'], // 12-C
        ['email' => 'isabel.torres@email.com',      'nombres' => 'Isabel',    'apellidos' => 'Torres',     'cedula' => 'V-10234567', 'cod_pint' => '03680'], // 12-D
        ['email' => 'miguel.ramos@email.com',       'nombres' => 'Miguel',    'apellidos' => 'Ramos',      'cedula' => 'V-11234567', 'cod_pint' => '03681'], // 12-E
        ['email' => 'adriana.castro@email.com',     'nombres' => 'Adriana',   'apellidos' => 'Castro',     'cedula' => 'V-12234567', 'cod_pint' => '03682'], // 12-F
        ['email' => 'fernando.mendez@email.com',    'nombres' => 'Fernando',  'apellidos' => 'Méndez',     'cedula' => 'V-13234567', 'cod_pint' => '03685'], // 13-C
        ['email' => 'luisa.vargas@email.com',       'nombres' => 'Luisa',     'apellidos' => 'Vargas',     'cedula' => 'V-14234567', 'cod_pint' => '03686'], // 13-D
        ['email' => 'roberto.flores@email.com',     'nombres' => 'Roberto',   'apellidos' => 'Flores',     'cedula' => 'V-15234567', 'cod_pint' => '03687'], // 13-E
        ['email' => 'cobranza@test.com',            'nombres' => 'Agente',    'apellidos' => 'Cobranza',   'cedula' => 'V-00000002', 'cod_pint' => null],    // sin apto (rol admin)
    ];

    public function run(): void
    {
        foreach ($this->asignaciones as $data) {
            // Crear o actualizar usuario
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['nombres'] . ' ' . $data['apellidos'],
                    'password'          => Hash::make('password'),
                    'activo'            => true,
                    'email_verified_at' => now(),
                ]
            );

            if (!$data['cod_pint']) continue; // sin apartamento, solo usuario

            if (!$user->hasRole('cliente-propietario')) {
                $user->assignRole('cliente-propietario');
            }

            // Crear o actualizar propietario vinculado al usuario
            $prop = Propietario::updateOrCreate(
                ['cedula' => $data['cedula']],
                [
                    'nombres'   => $data['nombres'],
                    'apellidos' => $data['apellidos'],
                    'cedula'    => $data['cedula'],
                    'email'     => $data['email'],
                    'direccion' => 'Caracas, Venezuela',
                    'estatus'   => true,
                    'user_id'   => $user->id,
                ]
            );

            // Buscar el apartamento real por cod_pint (estable entre reinicios)
            $apto = Apartamento::where('cod_pint', $data['cod_pint'])->first();
            if (!$apto) {
                $this->command->warn("PropietarioSeeder: apartamento cod_pint={$data['cod_pint']} no encontrado para {$data['email']}");
                continue;
            }

            // Vincular propietario ↔ apartamento (evitar duplicados)
            $exists = DB::table('propietario_apartamento')
                ->where('propietario_id', $prop->id)
                ->where('apartamento_id', $apto->id)
                ->exists();

            if (!$exists) {
                DB::table('propietario_apartamento')->insert([
                    'propietario_id'     => $prop->id,
                    'apartamento_id'     => $apto->id,
                    'propietario_actual' => true,
                    'fecha_desde'        => '2020-01-01',
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }

        $this->command->info('PropietarioSeeder: ' . (count($this->asignaciones) - 1) . ' propietarios vinculados a apartamentos reales del edificio 116.');
    }
}
