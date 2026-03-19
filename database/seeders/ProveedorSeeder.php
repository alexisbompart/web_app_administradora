<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor\Proveedor;

class ProveedorSeeder extends Seeder
{
    /**
     * Seed the proveedores table with demo suppliers.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'rif'                => 'J-00000001-1',
                'razon_social'       => 'Hidrocapital C.A.',
                'nombre_comercial'   => 'Hidrocapital',
                'direccion'          => 'Av. Boyacá, Caracas',
                'telefono'           => '0212-555-5001',
                'email'              => 'servicios@hidrocapital.com.ve',
                'contacto'           => 'Departamento Comercial',
                'tipo_contribuyente' => 'especial',
                'banco_id'           => 5,
                'cuenta_bancaria'    => '01140000000000000001',
                'activo'             => true,
            ],
            [
                'rif'                => 'J-00000002-2',
                'razon_social'       => 'Corporación Eléctrica Nacional S.A.',
                'nombre_comercial'   => 'Corpoelec',
                'direccion'          => 'Av. Libertador, Caracas',
                'telefono'           => '0212-555-5002',
                'email'              => 'atencion@corpoelec.gob.ve',
                'contacto'           => 'Departamento Comercial',
                'tipo_contribuyente' => 'especial',
                'banco_id'           => 5,
                'cuenta_bancaria'    => '01140000000000000002',
                'activo'             => true,
            ],
            [
                'rif'                => 'J-30000001-1',
                'razon_social'       => 'Servicios de Limpieza Express C.A.',
                'nombre_comercial'   => 'Servicios de Limpieza Express',
                'direccion'          => 'Calle Carabobo, La Candelaria, Caracas',
                'telefono'           => '0212-555-5003',
                'email'              => 'info@limpiezaexpress.com',
                'contacto'           => 'Gerencia General',
                'tipo_contribuyente' => 'ordinario',
                'banco_id'           => null,
                'activo'             => true,
            ],
            [
                'rif'                => 'J-30000002-2',
                'razon_social'       => 'Mantenimiento Integral VZ C.A.',
                'nombre_comercial'   => 'Mantenimiento Integral VZ',
                'direccion'          => 'Av. Urdaneta, Caracas',
                'telefono'           => '0212-555-5004',
                'email'              => 'contacto@mantenimientovz.com',
                'contacto'           => 'Ing. Roberto Sánchez',
                'tipo_contribuyente' => 'ordinario',
                'banco_id'           => 5,
                'cuenta_bancaria'    => '01140000000000000004',
                'activo'             => true,
            ],
            [
                'rif'                => 'J-30000003-3',
                'razon_social'       => 'Ascensores Nacional C.A.',
                'nombre_comercial'   => 'Ascensores Nacional',
                'direccion'          => 'Zona Industrial La Yaguara, Caracas',
                'telefono'           => '0212-555-5005',
                'email'              => 'ventas@ascensoresnacional.com',
                'contacto'           => 'Departamento Técnico',
                'tipo_contribuyente' => 'ordinario',
                'banco_id'           => null,
                'activo'             => true,
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::updateOrCreate(
                ['rif' => $proveedor['rif']],
                $proveedor
            );
        }
    }
}
