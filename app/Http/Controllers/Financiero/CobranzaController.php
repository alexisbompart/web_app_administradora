<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function pagosPendientes()
    {
        $pendientes = CondPago::where('estatus', 'P')
            ->with(['edificio', 'banco', 'registradoPor', 'condPagoAptos.apartamento.edificio', 'condPagoAptos.deuda'])
            ->latest('fecha_pago')
            ->paginate(20);

        $totalPendiente = CondPago::where('estatus', 'P')->sum('monto_total');
        $countPendiente = CondPago::where('estatus', 'P')->count();

        return view('financiero.pagos-pendientes', compact('pendientes', 'totalPendiente', 'countPendiente'));
    }

    public function verPago(CondPago $pago)
    {
        $pago->load(['edificio', 'banco', 'registradoPor', 'condPagoAptos.apartamento.edificio', 'condPagoAptos.deuda']);
        return view('financiero.ver-pago', compact('pago'));
    }

    public function aprobarPago(Request $request, CondPago $pago)
    {
        if ($pago->estatus !== 'P') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        DB::beginTransaction();
        try {
            $pago->update([
                'estatus'       => 'A',
                'monto_recibido'=> $pago->monto_total,
                'observaciones' => trim(($pago->observaciones ?? '') . ' | Aprobado por ' . Auth::user()->name . ' el ' . now()->format('d/m/Y H:i')),
            ]);

            // Mark each related deuda as cancelled
            foreach ($pago->condPagoAptos as $pagoApto) {
                if ($pagoApto->deuda_id) {
                    CondDeudaApto::where('id', $pagoApto->deuda_id)->update([
                        'estatus'       => 'C',
                        'monto_pagado'  => DB::raw('monto_original'),
                        'saldo'         => 0,
                        'fecha_pago'    => $pago->fecha_pago,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Pago #' . $pago->id . ' aprobado correctamente. Las deudas han sido marcadas como canceladas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    public function rechazarPago(Request $request, CondPago $pago)
    {
        $request->validate(['motivo' => 'required|string|max:500']);

        if ($pago->estatus !== 'P') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        $pago->update([
            'estatus'      => 'R',
            'observaciones'=> trim(($pago->observaciones ?? '') . ' | Rechazado: ' . $request->motivo . ' (' . Auth::user()->name . ' ' . now()->format('d/m/Y') . ')'),
        ]);

        return back()->with('success', 'Pago #' . $pago->id . ' rechazado. El propietario debera registrarlo nuevamente.');
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
