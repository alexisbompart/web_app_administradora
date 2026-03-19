<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;
use App\Models\Personal\Trabajador;

class TrabajadorSeeder extends Seeder
{
    /**
     * Seed the trabajadores table with demo workers.
     */
    public function run(): void
    {
        $compania = Compania::where('cod_compania', 'ADM001')->first();

        if (!$compania) {
            $this->command->error('Compania ADM001 not found. Run CompaniaSeeder first.');
            return;
        }

        $trabajadores = [
            [
                'compania_id'      => $compania->id,
                'cedula'           => 'V-20345678',
                'nombres'          => 'Ramón',
                'apellidos'        => 'Gutiérrez',
                'fecha_nacimiento' => '1980-05-15',
                'sexo'             => 'M',
                'direccion'        => 'Barrio Sucre, Petare, Caracas',
                'telefono'         => '0212-555-4001',
                'celular'          => '0414-555-4001',
                'email'            => 'ramon.gutierrez@integralca.com',
                'cargo'            => 'Conserje',
                'departamento'     => 'Mantenimiento',
                'fecha_ingreso'    => '2023-03-01',
                'salario_basico'   => 200.00,
                'tipo_contrato'    => 'fijo',
                'estatus'          => 'A',
            ],
            [
                'compania_id'      => $compania->id,
                'cedula'           => 'V-21345678',
                'nombres'          => 'Jorge',
                'apellidos'        => 'Pacheco',
                'fecha_nacimiento' => '1985-08-22',
                'sexo'             => 'M',
                'direccion'        => 'La Candelaria, Caracas',
                'telefono'         => '0212-555-4002',
                'celular'          => '0424-555-4002',
                'email'            => 'jorge.pacheco@integralca.com',
                'cargo'            => 'Vigilante',
                'departamento'     => 'Seguridad',
                'fecha_ingreso'    => '2023-06-15',
                'salario_basico'   => 180.00,
                'tipo_contrato'    => 'fijo',
                'estatus'          => 'A',
            ],
            [
                'compania_id'      => $compania->id,
                'cedula'           => 'V-22345678',
                'nombres'          => 'Manuel',
                'apellidos'        => 'Briceño',
                'fecha_nacimiento' => '1975-11-03',
                'sexo'             => 'M',
                'direccion'        => 'Catia, Caracas',
                'telefono'         => '0212-555-4003',
                'celular'          => '0412-555-4003',
                'email'            => 'manuel.briceno@integralca.com',
                'cargo'            => 'Jardinero',
                'departamento'     => 'Mantenimiento',
                'fecha_ingreso'    => '2024-01-10',
                'salario_basico'   => 150.00,
                'tipo_contrato'    => 'fijo',
                'estatus'          => 'A',
            ],
            [
                'compania_id'      => $compania->id,
                'cedula'           => 'V-23345678',
                'nombres'          => 'Héctor',
                'apellidos'        => 'Navarro',
                'fecha_nacimiento' => '1990-02-18',
                'sexo'             => 'M',
                'direccion'        => 'El Paraíso, Caracas',
                'telefono'         => '0212-555-4004',
                'celular'          => '0416-555-4004',
                'email'            => 'hector.navarro@integralca.com',
                'cargo'            => 'Mantenimiento',
                'departamento'     => 'Mantenimiento',
                'fecha_ingreso'    => '2023-09-01',
                'salario_basico'   => 250.00,
                'tipo_contrato'    => 'fijo',
                'estatus'          => 'A',
            ],
            [
                'compania_id'      => $compania->id,
                'cedula'           => 'V-24345678',
                'nombres'          => 'Daniela',
                'apellidos'        => 'Salazar',
                'fecha_nacimiento' => '1988-07-25',
                'sexo'             => 'F',
                'direccion'        => 'Altamira, Caracas',
                'telefono'         => '0212-555-4005',
                'celular'          => '0414-555-4005',
                'email'            => 'daniela.salazar@integralca.com',
                'cargo'            => 'Administrador de Edificio',
                'departamento'     => 'Administración',
                'fecha_ingreso'    => '2023-01-15',
                'salario_basico'   => 500.00,
                'tipo_contrato'    => 'fijo',
                'estatus'          => 'A',
            ],
        ];

        foreach ($trabajadores as $trabajador) {
            Trabajador::updateOrCreate(
                ['cedula' => $trabajador['cedula']],
                $trabajador
            );
        }
    }
}
