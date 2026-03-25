<?php

namespace App\Http\Controllers;

use App\Models\Financiero\CondPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('cliente-propietario')) {
            return redirect()->route('mi-condominio.dashboard');
        }

        if ($user->hasRole('proveedor')) {
            return redirect()->route('proveedores.facturas.index');
        }

        // Pagos por mes — últimos 12 meses (excluye rechazados)
        $pagosPorMes = DB::table('cond_pagos')
            ->whereNull('deleted_at')
            ->whereNotNull('fecha_pago')
            ->whereNotIn('estatus', ['R'])
            ->where('fecha_pago', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("to_char(fecha_pago, 'YYYY-MM') as mes, COUNT(*) as cantidad, SUM(monto_total) as monto")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        // Rellenar meses sin datos
        $mesesLabels = [];
        $mesesCantidad = [];
        $mesesMonto = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->translatedFormat('M y');
            $mesesLabels[] = $label;
            $mesesCantidad[] = $pagosPorMes[$key]->cantidad ?? 0;
            $mesesMonto[] = round($pagosPorMes[$key]->monto ?? 0, 2);
        }

        // Pagos pendientes de aprobación
        $pagosPendientes = CondPago::where('estatus', 'P')
            ->with(['banco', 'registradoPor', 'condPagoAptos.apartamento.edificio'])
            ->latest('fecha_pago')
            ->take(10)
            ->get();

        $totalPagosAprobados = CondPago::whereNotIn('estatus', ['P', 'R'])->sum('monto_total');
        $countPagosPendientes = CondPago::where('estatus', 'P')->count();

        return view('dashboard', compact(
            'mesesLabels', 'mesesCantidad', 'mesesMonto',
            'pagosPendientes', 'totalPagosAprobados', 'countPagosPendientes'
        ));
    }
}
