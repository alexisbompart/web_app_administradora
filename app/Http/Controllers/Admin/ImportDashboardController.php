<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ImportDashboardController extends Controller
{
    public function index()
    {
        $tables = [
            [
                'nombre' => 'Edificios',
                'tabla' => 'cond_edificios',
                'icono' => 'fas fa-city',
                'color' => 'blue',
                'ruta_import' => 'condominio.edificios.importar',
                'ruta_listado' => 'condominio.edificios.index',
                'tipo' => 'Incremental',
            ],
            [
                'nombre' => 'Apartamentos',
                'tabla' => 'cond_aptos',
                'icono' => 'fas fa-door-open',
                'color' => 'indigo',
                'ruta_import' => 'condominio.apartamentos.importar',
                'ruta_listado' => 'condominio.apartamentos.index',
                'tipo' => 'Incremental',
            ],
            [
                'nombre' => 'Deudas',
                'tabla' => 'cond_deudas_apto',
                'icono' => 'fas fa-file-invoice-dollar',
                'color' => 'red',
                'ruta_import' => 'financiero.deudas.importar',
                'ruta_listado' => 'financiero.deudas.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Descuentos',
                'tabla' => 'cond_descuentos_apto',
                'icono' => 'fas fa-percentage',
                'color' => 'purple',
                'ruta_import' => 'financiero.descuentos.importar',
                'ruta_listado' => 'financiero.descuentos.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Abonos',
                'tabla' => 'cond_abonos_apto',
                'icono' => 'fas fa-money-bill-wave',
                'color' => 'green',
                'ruta_import' => 'financiero.abonos.importar',
                'ruta_listado' => 'financiero.abonos.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Gastos',
                'tabla' => 'cond_gastos',
                'icono' => 'fas fa-receipt',
                'color' => 'amber',
                'ruta_import' => 'financiero.gastos.importar',
                'ruta_listado' => 'financiero.gastos.index',
                'tipo' => 'Incremental',
            ],
            [
                'nombre' => 'Pagos',
                'tabla' => 'cond_pagos',
                'icono' => 'fas fa-money-check-alt',
                'color' => 'cyan',
                'ruta_import' => 'financiero.pagos.importar',
                'ruta_listado' => 'financiero.pagos.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Pagos x Apto',
                'tabla' => 'cond_pago_aptos',
                'icono' => 'fas fa-credit-card',
                'color' => 'teal',
                'ruta_import' => 'financiero.pagoapto.importar',
                'ruta_listado' => 'financiero.pagos-apto.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Mov. Pre-fact',
                'tabla' => 'cond_movimientos_prefact',
                'icono' => 'fas fa-exchange-alt',
                'color' => 'orange',
                'ruta_import' => 'financiero.movprefact.importar',
                'ruta_listado' => 'financiero.mov-prefact.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Fact. x Apto',
                'tabla' => 'cond_movs_fact_apto',
                'icono' => 'fas fa-file-invoice',
                'color' => 'pink',
                'ruta_import' => 'financiero.movfactapto.importar',
                'ruta_listado' => 'financiero.fact-apto.index',
                'tipo' => 'Carga Completa',
            ],
            [
                'nombre' => 'Fact. x Edificio',
                'tabla' => 'cond_movs_fact_edif',
                'icono' => 'fas fa-building',
                'color' => 'slate',
                'ruta_import' => 'financiero.movfactedif.importar',
                'ruta_listado' => 'financiero.fact-edif.index',
                'tipo' => 'Carga Completa',
            ],
        ];

        foreach ($tables as &$t) {
            $stats = DB::selectOne("SELECT COUNT(*) as total, MAX(created_at) as ultima_creacion, MAX(updated_at) as ultima_actualizacion FROM {$t['tabla']}");
            $t['total'] = $stats->total ?? 0;
            $t['ultima_carga'] = $stats->ultima_actualizacion ?? $stats->ultima_creacion;
        }

        return view('admin.import-dashboard', compact('tables'));
    }
}
