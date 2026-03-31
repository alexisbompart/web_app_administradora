<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Catálogos (sin dependencias)
            EstadoSeeder::class,
            ParametroSeeder::class,
            ModuloSeeder::class,
            BancoSeeder::class,
            TasaBcvSeeder::class,

            // 2. Roles y Permisos (Spatie)
            RolePermissionSeeder::class,

            // 3. Entidades core
            CompaniaSeeder::class,
            EdificioSeeder::class,
            ApartamentoSeeder::class,

            // 4. Personas
            PropietarioSeeder::class,
            UserSeeder::class,

            // 5. Datos financieros base
            FondoSeeder::class,
            TrabajadorSeeder::class,
            ProveedorSeeder::class,

            // 6. Datos de demostración
            DatosDemoSeeder::class,

            // 7. Contenido pagina Welcome
            WelcomeContentSeeder::class,
        ]);
    }
}
