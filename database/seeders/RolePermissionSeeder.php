<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // sistema
            'sistema.ver-dashboard',
            'sistema.gestionar-usuarios',
            'sistema.gestionar-roles',
            'sistema.ver-auditoria',
            'sistema.configuracion',

            // personal
            'personal.ver',
            'personal.crear',
            'personal.editar',
            'personal.eliminar',
            'personal.aprobar-nomina',
            'personal.procesar-nomina',
            'personal.exportar',

            // proveedores
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',
            'proveedores.aprobar-factura',
            'proveedores.registrar-retencion',
            'proveedores.exportar',

            // fondos
            'fondos.ver',
            'fondos.crear',
            'fondos.editar',
            'fondos.eliminar',
            'fondos.aprobar-movimiento',
            'fondos.conciliar',
            'fondos.exportar',

            // cobranza
            'cobranza.ver',
            'cobranza.crear',
            'cobranza.editar',
            'cobranza.eliminar',
            'cobranza.registrar-pago',
            'cobranza.judicial',
            'cobranza.exportar',

            // pago-integral
            'pago-integral.ver',
            'pago-integral.crear',
            'pago-integral.procesar',
            'pago-integral.consultar-saldo',
            'pago-integral.exportar',

            // cajamatic
            'cajamatic.ver',
            'cajamatic.crear',
            'cajamatic.editar',
            'cajamatic.depositar',
            'cajamatic.exportar',

            // atencion-cliente
            'atencion-cliente.ver',
            'atencion-cliente.crear',
            'atencion-cliente.editar',
            'atencion-cliente.asignar-ejecutivo',
            'atencion-cliente.exportar',

            // informes
            'informes.ver',
            'informes.crear',
            'informes.exportar',
            'informes.ver-morosos',
            'informes.estado-cuenta',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 1. super-admin - Gate::before handles it
        Role::firstOrCreate(['name' => 'super-admin']);

        // 2. administrador - ALL permissions except sistema.configuracion
        $administrador = Role::firstOrCreate(['name' => 'administrador']);
        $administrador->syncPermissions(
            collect($permissions)->filter(fn ($p) => $p !== 'sistema.configuracion')->toArray()
        );

        // 3. gerente-contador
        $gerenteContador = Role::firstOrCreate(['name' => 'gerente-contador']);
        $gerenteContador->syncPermissions([
            'sistema.ver-dashboard',
            'personal.ver',
            'personal.exportar',
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.aprobar-factura',
            'proveedores.registrar-retencion',
            'proveedores.exportar',
            'fondos.ver',
            'fondos.crear',
            'fondos.editar',
            'fondos.aprobar-movimiento',
            'fondos.conciliar',
            'fondos.exportar',
            'cobranza.ver',
            'cobranza.exportar',
            'pago-integral.ver',
            'pago-integral.exportar',
            'cajamatic.ver',
            'cajamatic.exportar',
            'atencion-cliente.ver',
            'informes.ver',
            'informes.crear',
            'informes.exportar',
            'informes.ver-morosos',
            'informes.estado-cuenta',
        ]);

        // 4. personal-rrhh
        $personalRrhh = Role::firstOrCreate(['name' => 'personal-rrhh']);
        $personalRrhh->syncPermissions([
            'sistema.ver-dashboard',
            'personal.ver',
            'personal.crear',
            'personal.editar',
            'personal.eliminar',
            'personal.aprobar-nomina',
            'personal.procesar-nomina',
            'personal.exportar',
        ]);

        // 5. proveedor
        $proveedor = Role::firstOrCreate(['name' => 'proveedor']);
        $proveedor->syncPermissions([
            'sistema.ver-dashboard',
            'proveedores.ver',
        ]);

        // 6. cliente-propietario
        $clientePropietario = Role::firstOrCreate(['name' => 'cliente-propietario']);
        $clientePropietario->syncPermissions([
            'sistema.ver-dashboard',
            'pago-integral.ver',
            'pago-integral.consultar-saldo',
            'informes.ver',
            'informes.estado-cuenta',
            'atencion-cliente.ver',
            'atencion-cliente.crear',
        ]);

        // 7. cobranza
        $cobranza = Role::firstOrCreate(['name' => 'cobranza']);
        $cobranza->syncPermissions([
            'sistema.ver-dashboard',
            'cobranza.ver',
            'cobranza.crear',
            'cobranza.editar',
            'cobranza.eliminar',
            'cobranza.registrar-pago',
            'cobranza.judicial',
            'cobranza.exportar',
            'pago-integral.ver',
            'pago-integral.procesar',
            'cajamatic.ver',
            'cajamatic.crear',
            'cajamatic.depositar',
            'informes.ver',
            'informes.ver-morosos',
            'atencion-cliente.ver',
            'atencion-cliente.crear',
        ]);
    }
}
