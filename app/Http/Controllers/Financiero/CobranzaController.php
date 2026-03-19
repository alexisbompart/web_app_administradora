<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobranzaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cobranza.ver')->only(['index', 'morosos']);
        $this->middleware('permission:cobranza.registrar-pago')->only(['registrarPago']);
        $this->middleware('permission:cobranza.judicial')->only(['gestionJudicial']);
    }

    public function index()
    {
        $query = CondDeudaApto::with(['apartamento', 'edificio']);

        if (request('filtro') === 'pendientes') {
            $query->where('estatus', 'P');
        } elseif (request('filtro') === 'canceladas') {
            $query->where('estatus', 'C');
        }

        $deudas = $query->paginate(15);

        $stats = [
            'pendientes_count' => CondDeudaApto::where('estatus', 'P')->count(),
            'monto_total_pendiente' => CondDeudaApto::where('estatus', 'P')->sum('saldo'),
            'morosos_count' => CondDeudaApto::where('estatus', 'P')
                ->selectRaw('apartamento_id, COUNT(*) as meses')
                ->groupBy('apartamento_id')
                ->having(\DB::raw('COUNT(*)'), '>=', 3)
                ->get()
                ->count(),
        ];

        return view('financiero.cobranza', compact('deudas', 'stats'));
    }

    public function registrarPago(Request $request)
    {
        $pago = CondPago::create($request->all());

        return response()->json([
            'message' => 'Pago registrado exitosamente',
            'module'  => 'Financiero',
            'data'    => $pago,
        ], 201);
    }

    public function morosos()
    {
        $morosos = CondDeudaApto::where('estatus', 'P')
            ->select('apartamento_id', DB::raw('COUNT(*) as meses_vencidos'), DB::raw('SUM(saldo) as total_deuda'))
            ->groupBy('apartamento_id')
            ->having(DB::raw('COUNT(*)'), '>=', 2)
            ->orderByDesc('total_deuda')
            ->get()
            ->map(function ($item) {
                $item->apartamento = \App\Models\Condominio\Apartamento::with('edificio')->find($item->apartamento_id);
                return $item;
            });

        $totalMorosos = $morosos->count();
        $totalAdeudado = $morosos->sum('total_deuda');

        return view('financiero.morosos', compact('morosos', 'totalMorosos', 'totalAdeudado'));
    }

    public function gestionJudicial(Request $request)
    {
        $morosos = CondDeudaApto::where('estatus', 'P')
            ->select('apartamento_id', DB::raw('COUNT(*) as meses_vencidos'), DB::raw('SUM(saldo) as total_deuda'))
            ->groupBy('apartamento_id')
            ->having(DB::raw('COUNT(*)'), '>=', 3)
            ->orderByDesc('total_deuda')
            ->get()
            ->map(function ($item) {
                $item->apartamento = \App\Models\Condominio\Apartamento::with('edificio')->find($item->apartamento_id);
                return $item;
            });

        $totalMorosos = $morosos->count();
        $totalAdeudado = $morosos->sum('total_deuda');

        return view('financiero.judicial', compact('morosos', 'totalMorosos', 'totalAdeudado'));
    }
}
