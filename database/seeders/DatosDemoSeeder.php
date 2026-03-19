<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Apartamento;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use App\Models\Financiero\Fondo;
use App\Models\Financiero\MovimientoFondo;
use App\Models\Proveedor\FacturaProveedor;
use App\Models\Proveedor\Proveedor;

class DatosDemoSeeder extends Seeder
{
    /**
     * Seed demo data: debts, payments, supplier invoices, and fund movements.
     */
    public function run(): void
    {
        $compania = Compania::where('cod_compania', 'ADM001')->first();
        $edificio = Edificio::where('cod_edif', 'TORRE-A')->first();

        if (!$compania || !$edificio) {
            $this->command->error('Compania or Edificio not found. Run previous seeders first.');
            return;
        }

        $this->seedDeudas($compania, $edificio);
        $this->seedPagos($compania, $edificio);
        $this->seedFacturasProveedores($compania);
        $this->seedMovimientosFondos($compania);
    }

    /**
     * Create debts for all TORRE-A apartments for the last 3 months.
     */
    private function seedDeudas(Compania $compania, Edificio $edificio): void
    {
        $apartamentos = Apartamento::where('edificio_id', $edificio->id)->get();
        $periodos = ['2026-01', '2026-02', '2026-03'];

        // About 30% of apartments will be morosos (pending for all 3 months)
        $totalAptos = $apartamentos->count();
        $morososCount = (int) ceil($totalAptos * 0.3);
        $morososIds = $apartamentos->pluck('id')->shuffle()->take($morososCount)->toArray();

        foreach ($apartamentos as $apartamento) {
            $esMoroso = in_array($apartamento->id, $morososIds);

            foreach ($periodos as $periodo) {
                $montoOriginal = rand(8000, 20000) / 100; // 80.00 - 200.00
                $fechaEmision = $periodo . '-01';
                $fechaVencimiento = $periodo . '-15';

                if ($esMoroso) {
                    // Moroso: all months pending
                    $estatus = 'P';
                    $montoPagado = 0;
                    $saldo = $montoOriginal;
                } else {
                    // Non-moroso: older months paid, current month pending
                    if ($periodo === '2026-03') {
                        $estatus = 'P';
                        $montoPagado = 0;
                        $saldo = $montoOriginal;
                    } else {
                        $estatus = 'C';
                        $montoPagado = $montoOriginal;
                        $saldo = 0;
                    }
                }

                CondDeudaApto::updateOrCreate(
                    [
                        'compania_id'    => $compania->id,
                        'edificio_id'    => $edificio->id,
                        'apartamento_id' => $apartamento->id,
                        'periodo'        => $periodo,
                    ],
                    [
                        'fecha_emision'     => $fechaEmision,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'monto_original'    => $montoOriginal,
                        'monto_mora'        => 0,
                        'monto_interes'     => 0,
                        'monto_descuento'   => 0,
                        'monto_pagado'      => $montoPagado,
                        'saldo'             => $saldo,
                        'estatus'           => $estatus,
                    ]
                );
            }
        }
    }

    /**
     * Create 10 payment records for paid debts.
     */
    private function seedPagos(Compania $compania, Edificio $edificio): void
    {
        $deudasPagadas = CondDeudaApto::where('compania_id', $compania->id)
            ->where('edificio_id', $edificio->id)
            ->where('estatus', 'C')
            ->take(10)
            ->get();

        if ($deudasPagadas->isEmpty()) {
            $this->command->warn('No paid debts found to create payments.');
            return;
        }

        $formasPago = ['transferencia', 'deposito', 'efectivo'];

        foreach ($deudasPagadas as $index => $deuda) {
            $formaPago = $formasPago[$index % count($formasPago)];
            $numeroRecibo = 'REC-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT);
            $fechaPago = $deuda->periodo . '-' . str_pad(rand(5, 14), 2, '0', STR_PAD_LEFT);

            $pago = CondPago::updateOrCreate(
                ['numero_recibo' => $numeroRecibo],
                [
                    'compania_id'      => $compania->id,
                    'edificio_id'      => $edificio->id,
                    'fecha_pago'       => $fechaPago,
                    'forma_pago'       => $formaPago,
                    'banco_id'         => $formaPago !== 'efectivo' ? 5 : null,
                    'numero_referencia' => $formaPago !== 'efectivo' ? 'REF' . rand(100000, 999999) : null,
                    'monto_total'      => $deuda->monto_original,
                    'monto_recibido'   => $deuda->monto_original,
                    'estatus'          => 'A',
                ]
            );

            // Link payment to apartment
            CondPagoApto::updateOrCreate(
                [
                    'pago_id'        => $pago->id,
                    'apartamento_id' => $deuda->apartamento_id,
                    'deuda_id'       => $deuda->id,
                ],
                [
                    'periodo'        => $deuda->periodo,
                    'monto_aplicado' => $deuda->monto_original,
                ]
            );
        }
    }

    /**
     * Create 5 supplier invoices.
     */
    private function seedFacturasProveedores(Compania $compania): void
    {
        $proveedores = Proveedor::take(5)->get();

        if ($proveedores->isEmpty()) {
            $this->command->warn('No providers found. Run ProveedorSeeder first.');
            return;
        }

        $facturas = [
            [
                'proveedor_index'   => 0,
                'numero_factura'    => 'FAC-2026-001',
                'numero_control'    => '00-000001',
                'fecha_factura'     => '2026-01-15',
                'fecha_recepcion'   => '2026-01-16',
                'fecha_vencimiento' => '2026-02-15',
                'subtotal'          => 5000.00,
                'base_imponible'    => 5000.00,
                'monto_exento'      => 0,
                'iva_porcentaje'    => 16.00,
                'iva_monto'         => 800.00,
                'total'             => 5800.00,
                'estatus'           => 'pagada',
                'observaciones'     => 'Servicio de agua enero 2026',
            ],
            [
                'proveedor_index'   => 1,
                'numero_factura'    => 'FAC-2026-002',
                'numero_control'    => '00-000002',
                'fecha_factura'     => '2026-01-20',
                'fecha_recepcion'   => '2026-01-21',
                'fecha_vencimiento' => '2026-02-20',
                'subtotal'          => 3500.00,
                'base_imponible'    => 3500.00,
                'monto_exento'      => 0,
                'iva_porcentaje'    => 16.00,
                'iva_monto'         => 560.00,
                'total'             => 4060.00,
                'estatus'           => 'pagada',
                'observaciones'     => 'Servicio eléctrico enero 2026',
            ],
            [
                'proveedor_index'   => 2,
                'numero_factura'    => 'FAC-2026-003',
                'numero_control'    => '00-000003',
                'fecha_factura'     => '2026-02-05',
                'fecha_recepcion'   => '2026-02-06',
                'fecha_vencimiento' => '2026-03-05',
                'subtotal'          => 2000.00,
                'base_imponible'    => 2000.00,
                'monto_exento'      => 0,
                'iva_porcentaje'    => 16.00,
                'iva_monto'         => 320.00,
                'total'             => 2320.00,
                'estatus'           => 'pendiente',
                'observaciones'     => 'Servicio de limpieza febrero 2026',
            ],
            [
                'proveedor_index'   => 3,
                'numero_factura'    => 'FAC-2026-004',
                'numero_control'    => '00-000004',
                'fecha_factura'     => '2026-02-15',
                'fecha_recepcion'   => '2026-02-16',
                'fecha_vencimiento' => '2026-03-15',
                'subtotal'          => 8000.00,
                'base_imponible'    => 8000.00,
                'monto_exento'      => 0,
                'iva_porcentaje'    => 16.00,
                'iva_monto'         => 1280.00,
                'total'             => 9280.00,
                'estatus'           => 'pendiente',
                'observaciones'     => 'Mantenimiento general febrero 2026',
            ],
            [
                'proveedor_index'   => 4,
                'numero_factura'    => 'FAC-2026-005',
                'numero_control'    => '00-000005',
                'fecha_factura'     => '2026-03-01',
                'fecha_recepcion'   => '2026-03-02',
                'fecha_vencimiento' => '2026-04-01',
                'subtotal'          => 12000.00,
                'base_imponible'    => 12000.00,
                'monto_exento'      => 0,
                'iva_porcentaje'    => 16.00,
                'iva_monto'         => 1920.00,
                'total'             => 13920.00,
                'estatus'           => 'pendiente',
                'observaciones'     => 'Mantenimiento ascensores marzo 2026',
            ],
        ];

        foreach ($facturas as $factura) {
            $proveedor = $proveedores[$factura['proveedor_index']] ?? $proveedores->first();
            unset($factura['proveedor_index']);

            FacturaProveedor::updateOrCreate(
                ['numero_factura' => $factura['numero_factura']],
                array_merge($factura, [
                    'proveedor_id' => $proveedor->id,
                    'compania_id'  => $compania->id,
                ])
            );
        }
    }

    /**
     * Create fund movements for Fondo de Contingencias.
     */
    private function seedMovimientosFondos(Compania $compania): void
    {
        $fondo = Fondo::where('compania_id', $compania->id)
            ->where('tipo', 'contingencias')
            ->first();

        if (!$fondo) {
            $this->command->warn('Fondo de Contingencias not found. Run FondoSeeder first.');
            return;
        }

        $movimientos = [
            [
                'fondo_id'          => $fondo->id,
                'tipo_movimiento'   => 'I',
                'monto'             => 10000.00,
                'saldo_anterior'    => 0.00,
                'saldo_posterior'   => 10000.00,
                'descripcion'       => 'Aporte inicial al fondo de contingencias',
                'referencia'        => 'MOV-FONDO-001',
                'fecha_movimiento'  => '2026-01-05',
            ],
            [
                'fondo_id'          => $fondo->id,
                'tipo_movimiento'   => 'I',
                'monto'             => 20000.00,
                'saldo_anterior'    => 10000.00,
                'saldo_posterior'   => 30000.00,
                'descripcion'       => 'Aporte mensual febrero 2026',
                'referencia'        => 'MOV-FONDO-002',
                'fecha_movimiento'  => '2026-02-05',
            ],
            [
                'fondo_id'          => $fondo->id,
                'tipo_movimiento'   => 'E',
                'monto'             => 3000.00,
                'saldo_anterior'    => 30000.00,
                'saldo_posterior'   => 27000.00,
                'descripcion'       => 'Reparación de emergencia en tuberías',
                'referencia'        => 'MOV-FONDO-003',
                'fecha_movimiento'  => '2026-02-20',
            ],
            [
                'fondo_id'          => $fondo->id,
                'tipo_movimiento'   => 'E',
                'monto'             => 2000.00,
                'saldo_anterior'    => 27000.00,
                'saldo_posterior'   => 25000.00,
                'descripcion'       => 'Reparación sistema eléctrico áreas comunes',
                'referencia'        => 'MOV-FONDO-004',
                'fecha_movimiento'  => '2026-03-10',
            ],
        ];

        foreach ($movimientos as $movimiento) {
            MovimientoFondo::updateOrCreate(
                ['referencia' => $movimiento['referencia']],
                $movimiento
            );
        }
    }
}
