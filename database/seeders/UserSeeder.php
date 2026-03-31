<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'super-admin',
            ],
            [
                'name' => 'Administrador',
                'email' => 'administrador@test.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'administrador',
            ],
            [
                'name' => 'Gerente Contador',
                'email' => 'gerente@test.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'gerente-contador',
            ],
            [
                'name' => 'Personal RRHH',
                'email' => 'rrhh@test.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'personal-rrhh',
            ],
            [
                'name' => 'Proveedor Demo',
                'email' => 'proveedor@test.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'proveedor',
            ],
            // cliente-propietario users are now created by PropietarioSeeder
            // with each propietario's own email and linked user_id
            [
                'name' => 'Agente Cobranza',
                'email' => 'cobranza@test.com',
                'password' => Hash::make('password'),
                'activo' => true,
                'email_verified_at' => now(),
                'role' => 'cobranza',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }

        // Vincular usuario proveedor con el primer proveedor
        $proveedorUser = User::where('email', 'proveedor@test.com')->first();
        $proveedor = \App\Models\Proveedor\Proveedor::first();
        if ($proveedorUser && $proveedor) {
            $proveedor->update(['user_id' => $proveedorUser->id]);
        }
    }
}
