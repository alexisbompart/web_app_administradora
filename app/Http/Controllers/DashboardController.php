<?php

namespace App\Http\Controllers;

use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Número de meses de deuda pendiente para considerar extrajudicial (fallback si el edificio no tiene configurado meses_extjud)
    const MESES_EXTRAJUDICIAL = 4;

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

        // Apartamentos en extrajudicial: más de N meses de deuda pendiente
        $extrajudicialData = $this->getExtrajudicialData();

        // Deudas pendientes agrupadas por edificio → apartamento
        $deudasData = $this->getDeudasData();

        // KPI Charts
        $kpiData = $this->getKpiData();

        return view('dashboard', compact(
            'mesesLabels', 'mesesCantidad', 'mesesMonto',
            'pagosPendientes', 'totalPagosAprobados', 'countPagosPendientes',
            'extrajudicialData', 'deudasData', 'kpiData'
        ));
    }

    private function getExtrajudicialData(): array
    {
        $umbral = self::MESES_EXTRAJUDICIAL;

        // Agrupar por apartamento y contar meses pendientes.
        // Usa meses_extjud del edificio si está configurado, si no usa el fallback.
        $porApartamento = DB::table('cond_deudas_apto as d')
            ->join('cond_aptos as a', 'a.id', '=', 'd.apartamento_id')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->where('e.activo', true)
            ->where(function ($q) {
                $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01');
            })
            ->where(function ($q) {
                $q->whereNull('d.serial')->orWhere('d.serial', 'N');
            })
            ->selectRaw("
                d.apartamento_id,
                d.edificio_id,
                a.num_apto,
                e.nombre as edificio_nombre,
                COALESCE(e.meses_extjud, ?) as umbral_edificio,
                COUNT(*) as meses_deuda,
                SUM(d.saldo) as total_saldo
            ", [$umbral])
            ->groupBy('d.apartamento_id', 'd.edificio_id', 'a.num_apto', 'e.nombre', 'e.meses_extjud')
            ->havingRaw('COUNT(*) > COALESCE(e.meses_extjud, ?)', [$umbral])
            ->orderBy('e.nombre')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->get();

        // Contar total de apartamentos en extrajudicial
        $totalApartamentos = $porApartamento->count();

        // Agrupar por edificio para el drilldown
        $porEdificio = $porApartamento
            ->groupBy('edificio_id')
            ->map(function ($aptos, $edificioId) {
                return [
                    'edificio_id'     => $edificioId,
                    'edificio_nombre' => $aptos->first()->edificio_nombre,
                    'total_aptos'     => $aptos->count(),
                    'total_saldo'     => $aptos->sum('total_saldo'),
                    'apartamentos'    => $aptos->map(fn($a) => [
                        'num_apto'    => $a->num_apto,
                        'meses_deuda' => $a->meses_deuda,
                        'total_saldo' => $a->total_saldo,
                    ])->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return [
            'total_apartamentos' => $totalApartamentos,
            'por_edificio'       => $porEdificio,
            'umbral_meses'       => $umbral,
        ];
    }

    private function getKpiData(): array
    {
        // 1. Distribución de morosos por rango de meses
        $porRango = DB::table('cond_deudas_apto as d')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->where('e.activo', true)
            ->where(function ($q) { $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01'); })
            ->where(function ($q) { $q->whereNull('d.serial')->orWhere('d.serial', 'N'); })
            ->selectRaw('d.apartamento_id, COUNT(*) as meses')
            ->groupBy('d.apartamento_id')
            ->get()
            ->groupBy(function ($r) {
                if ($r->meses <= 3)  return '1-3 m';
                if ($r->meses <= 6)  return '4-6 m';
                if ($r->meses <= 12) return '7-12 m';
                if ($r->meses <= 24) return '13-24 m';
                return '+24 m';
            })
            ->map(fn($g) => $g->count());

        $rangosOrden = ['1-3 m', '4-6 m', '7-12 m', '13-24 m', '+24 m'];
        $rangosLabels  = $rangosOrden;
        $rangosValues  = array_map(fn($k) => $porRango[$k] ?? 0, $rangosOrden);

        // 2. Top 7 edificios con mayor deuda
        $topEdificios = DB::table('cond_deudas_apto as d')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->where('e.activo', true)
            ->where(function ($q) { $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01'); })
            ->where(function ($q) { $q->whereNull('d.serial')->orWhere('d.serial', 'N'); })
            ->selectRaw('e.nombre, SUM(d.saldo) as total_saldo, COUNT(DISTINCT d.apartamento_id) as aptos')
            ->groupBy('e.nombre')
            ->orderByDesc('total_saldo')
            ->limit(7)
            ->get();

        // 3. Eficiencia de cobranza: pagado vs deuda total
        $totalDeuda  = DB::table('cond_deudas_apto')
            ->where(function ($q) { $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01'); })
            ->where(function ($q) { $q->whereNull('serial')->orWhere('serial', 'N'); })
            ->sum('saldo');
        $totalPagado = DB::table('cond_pagos')->whereNotIn('estatus', ['P', 'R'])->sum('monto_total');

        // 4d. Edificios facturados por compañía y mes — año actual (líneas comparativas)
        $anioKpi = now()->year;
        $mesesNombresKpi = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        $edifPorCompMes = DB::table('cond_deudas_apto as d')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->join('cond_companias as c', 'c.id', '=', 'e.compania_id')
            ->where('e.activo', true)
            ->whereYear('d.fecha_emision', $anioKpi)
            ->selectRaw("c.id, c.nombre, to_char(d.fecha_emision,'MM') as mes, COUNT(DISTINCT d.edificio_id) as edificios")
            ->groupBy('c.id', 'c.nombre', 'mes')
            ->orderBy('c.nombre')->orderBy('mes')
            ->get();

        // Agrupar por compañía → array de 12 valores (uno por mes)
        $companiasMeses = $edifPorCompMes
            ->groupBy('id')
            ->map(function ($rows) use ($mesesNombresKpi) {
                $byMes = $rows->keyBy('mes');
                return [
                    'nombre' => $rows->first()->nombre,
                    'data'   => array_map(
                        fn($m) => (int)($byMes[str_pad($m, 2, '0', STR_PAD_LEFT)]->edificios ?? 0),
                        range(1, 12)
                    ),
                ];
            })
            ->values()
            ->toArray();

        // 4c. Edificios / aptos / deuda por compañía
        $porCompania = DB::table('cond_edificios as e')
            ->join('cond_companias as c', 'c.id', '=', 'e.compania_id')
            ->where('e.activo', true)
            ->selectRaw('c.id, c.nombre, COUNT(*) as total_edificios')
            ->groupBy('c.id', 'c.nombre')
            ->orderByDesc('total_edificios')
            ->get();

        $aptosCompania = DB::table('cond_aptos as a')
            ->join('cond_edificios as e', 'e.id', '=', 'a.edificio_id')
            ->join('cond_companias as c', 'c.id', '=', 'e.compania_id')
            ->where('e.activo', true)
            ->selectRaw('c.id, COUNT(*) as total_aptos')
            ->groupBy('c.id')
            ->get()->keyBy('id');

        $deudaCompania = DB::table('cond_deudas_apto as d')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->join('cond_companias as c', 'c.id', '=', 'e.compania_id')
            ->where('e.activo', true)
            ->where(function ($q) { $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01'); })
            ->where(function ($q) { $q->whereNull('d.serial')->orWhere('d.serial', 'N'); })
            ->selectRaw('c.id, COUNT(DISTINCT d.apartamento_id) as aptos_morosos, SUM(d.saldo) as saldo_total')
            ->groupBy('c.id')
            ->get()->keyBy('id');

        $companiaData = [
            'labels'         => $porCompania->map(fn($c) => strlen($c->nombre) > 28 ? substr($c->nombre,0,28).'…' : $c->nombre)->values()->toArray(),
            'nombres_full'   => $porCompania->pluck('nombre')->values()->toArray(),
            'edificios'      => $porCompania->pluck('total_edificios')->values()->toArray(),
            'aptos'          => $porCompania->map(fn($c) => (int)($aptosCompania[$c->id]->total_aptos ?? 0))->values()->toArray(),
            'aptos_morosos'  => $porCompania->map(fn($c) => (int)($deudaCompania[$c->id]->aptos_morosos ?? 0))->values()->toArray(),
            'saldo_deuda'    => $porCompania->map(fn($c) => round($deudaCompania[$c->id]->saldo_total ?? 0, 2))->values()->toArray(),
        ];

        // 4b. Pagos por mes año actual (todos los 12 meses, rellena 0 si no hay)
        $anioActual = now()->year;
        $pagosAnio = DB::table('cond_pagos')
            ->whereNull('deleted_at')
            ->whereNotNull('fecha_pago')
            ->whereNotIn('estatus', ['R', 'P'])
            ->whereYear('fecha_pago', $anioActual)
            ->selectRaw("to_char(fecha_pago, 'MM') as mes, COUNT(*) as c, SUM(monto_total) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $pagosAnioLabels  = $mesesNombres;
        $pagosAnioMontos  = [];
        $pagosAnioCounts  = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            $pagosAnioMontos[]  = round($pagosAnio[$key]->total ?? 0, 2);
            $pagosAnioCounts[]  = (int)($pagosAnio[$key]->c ?? 0);
        }

        // 4. Pagos por mes últimos 6 meses
        $pagosMes6 = DB::table('cond_pagos')
            ->whereNull('deleted_at')
            ->whereNotNull('fecha_pago')
            ->whereNotIn('estatus', ['R'])
            ->where('fecha_pago', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("to_char(fecha_pago, 'YYYY-MM') as mes, COUNT(*) as c, SUM(monto_total) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $labels6 = [];
        $montos6  = [];
        $counts6  = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $labels6[] = now()->subMonths($i)->translatedFormat('M y');
            $montos6[]  = round($pagosMes6[$key]->total ?? 0, 2);
            $counts6[]  = (int)($pagosMes6[$key]->c ?? 0);
        }

        // 5. Fondos saldo actual
        $fondos = DB::table('fondos')->where('activo', true)->get(['nombre', 'saldo_actual']);

        // 6. Apartamentos: pagados vs morosos vs extrajudicial
        $totalAptos    = DB::table('cond_aptos')->count();
        $aptosConDeuda = DB::table('cond_deudas_apto as d')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->where('e.activo', true)
            ->where(function ($q) { $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01'); })
            ->where(function ($q) { $q->whereNull('d.serial')->orWhere('d.serial', 'N'); })
            ->distinct('d.apartamento_id')
            ->count('d.apartamento_id');
        $aptosExtrajud = $this->getExtrajudicialData()['total_apartamentos'];
        $aptosAlDia    = max(0, $totalAptos - $aptosConDeuda);

        return [
            'rangos'         => ['labels' => $rangosLabels, 'values' => $rangosValues],
            'topEdificios'   => [
                'labels' => $topEdificios->pluck('nombre')->map(fn($n) => strlen($n) > 22 ? substr($n,0,22).'…' : $n)->values()->toArray(),
                'values' => $topEdificios->pluck('total_saldo')->map(fn($v) => round($v, 2))->values()->toArray(),
                'aptos'  => $topEdificios->pluck('aptos')->values()->toArray(),
                'nombres_completos' => $topEdificios->pluck('nombre')->values()->toArray(),
            ],
            'cobranza'       => ['deuda' => round($totalDeuda, 2), 'pagado' => round($totalPagado, 2)],
            'pagosMes6'      => ['labels' => $labels6, 'montos' => $montos6, 'counts' => $counts6],
            'pagosAnio'      => ['labels' => $pagosAnioLabels, 'montos' => $pagosAnioMontos, 'counts' => $pagosAnioCounts, 'anio' => $anioActual],
            'companiaData'   => $companiaData,
            'companiasMeses' => ['series' => $companiasMeses, 'anio' => $anioKpi],
            'fondos'         => ['labels' => $fondos->pluck('nombre')->toArray(), 'values' => $fondos->pluck('saldo_actual')->map(fn($v) => round($v, 2))->toArray()],
            'estadoAptos'    => [
                'al_dia'      => $aptosAlDia,
                'morosos'     => max(0, $aptosConDeuda - $aptosExtrajud),
                'extrajudicial' => $aptosExtrajud,
                'total'       => $totalAptos,
            ],
        ];
    }

    private function getDeudasData(): array
    {
        $porApartamento = DB::table('cond_deudas_apto as d')
            ->join('cond_aptos as a', 'a.id', '=', 'd.apartamento_id')
            ->join('cond_edificios as e', 'e.id', '=', 'd.edificio_id')
            ->where('e.activo', true)
            ->where(function ($q) {
                $q->whereNull('d.fecha_pag')->orWhere('d.fecha_pag', '0001-01-01');
            })
            ->where(function ($q) {
                $q->whereNull('d.serial')->orWhere('d.serial', 'N');
            })
            ->selectRaw("
                d.apartamento_id,
                d.edificio_id,
                a.num_apto,
                e.nombre as edificio_nombre,
                COUNT(*) as meses_deuda,
                SUM(d.saldo) as total_saldo
            ")
            ->groupBy('d.apartamento_id', 'd.edificio_id', 'a.num_apto', 'e.nombre')
            ->orderBy('e.nombre')
            ->orderByDesc(DB::raw('SUM(d.saldo)'))
            ->get();

        $totalApartamentos = $porApartamento->count();
        $totalSaldo        = $porApartamento->sum('total_saldo');
        $totalMeses        = $porApartamento->sum('meses_deuda');

        $porEdificio = $porApartamento
            ->groupBy('edificio_id')
            ->map(function ($aptos, $edificioId) {
                return [
                    'edificio_id'     => $edificioId,
                    'edificio_nombre' => $aptos->first()->edificio_nombre,
                    'total_aptos'     => $aptos->count(),
                    'total_meses'     => $aptos->sum('meses_deuda'),
                    'total_saldo'     => $aptos->sum('total_saldo'),
                    'apartamentos'    => $aptos->map(fn($a) => [
                        'num_apto'    => $a->num_apto,
                        'meses_deuda' => $a->meses_deuda,
                        'total_saldo' => $a->total_saldo,
                    ])->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return [
            'total_apartamentos' => $totalApartamentos,
            'total_saldo'        => $totalSaldo,
            'total_meses'        => $totalMeses,
            'por_edificio'       => $porEdificio,
        ];
    }
}
