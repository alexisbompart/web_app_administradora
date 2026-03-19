<?php

namespace App\Http\Controllers\PagoIntegral;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\PagoIntegral;
use App\Models\Financiero\PagoIntegralDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoIntegralController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pago-integral.ver')->only(['index', 'consultarSaldo', 'comprobante']);
        $this->middleware('permission:pago-integral.procesar')->only(['procesarPago']);
    }

    public function index()
    {
        $pagos = PagoIntegral::latest()->paginate(15);

        return view('financiero.pago-integral', compact('pagos'));
    }

    public function consultarSaldo(Request $request)
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $apartamentos = Apartamento::with('edificio')->orderBy('num_apto')->get();

        $apartamento = null;
        $deudas = collect();

        if ($request->filled('apartamento_id')) {
            $apartamento = Apartamento::with('edificio')->find($request->apartamento_id);

            if ($apartamento) {
                $deudas = CondDeudaApto::where('apartamento_id', $apartamento->id)
                    ->where('estatus', 'P')
                    ->where('saldo', '>', 0)
                    ->orderBy('periodo')
                    ->get();
            }
        }

        return view('financiero.pago-integral-saldo', compact(
            'edificios',
            'apartamentos',
            'apartamento',
            'deudas'
        ));
    }

    public function procesarPago(Request $request)
    {
        // GET-like behavior: show the payment form with selected debts
        if (!$request->has('confirmar')) {
            $request->validate([
                'apartamento_id' => 'required|exists:cond_aptos,id',
                'deudas' => 'required|array|min:1',
                'deudas.*' => 'exists:cond_deudas_apto,id',
            ]);

            $apartamento = Apartamento::with('edificio')->findOrFail($request->apartamento_id);
            $deudas = CondDeudaApto::whereIn('id', $request->deudas)
                ->where('apartamento_id', $apartamento->id)
                ->where('estatus', 'P')
                ->orderBy('periodo')
                ->get();

            if ($deudas->isEmpty()) {
                return redirect()->route('financiero.pago-integral.consultar-saldo')
                    ->with('error', 'No se encontraron deudas pendientes para procesar.');
            }

            $total = $deudas->sum('saldo');

            return view('financiero.pago-integral-procesar', compact('apartamento', 'deudas', 'total'));
        }

        // POST with confirmar: actually process the payment
        $request->validate([
            'apartamento_id' => 'required|exists:cond_aptos,id',
            'deudas' => 'required|array|min:1',
            'deudas.*' => 'exists:cond_deudas_apto,id',
            'forma_pago' => 'required|string',
            'referencia' => 'required|string|max:50',
            'monto_total' => 'required|numeric|min:0.01',
        ]);

        $apartamento = Apartamento::with('edificio')->findOrFail($request->apartamento_id);
        $deudas = CondDeudaApto::whereIn('id', $request->deudas)
            ->where('apartamento_id', $apartamento->id)
            ->where('estatus', 'P')
            ->get();

        if ($deudas->isEmpty()) {
            return redirect()->route('financiero.pago-integral.consultar-saldo')
                ->with('error', 'No se encontraron deudas pendientes para procesar.');
        }

        $total = $deudas->sum('saldo');

        $pago = DB::transaction(function () use ($request, $deudas, $total, $apartamento) {
            $pago = PagoIntegral::create([
                'compania_id' => $apartamento->edificio->compania_id ?? null,
                'fecha' => now(),
                'monto_total' => $total,
                'forma_pago' => $request->forma_pago,
                'referencia' => $request->referencia,
                'estatus' => 'P',
                'observaciones' => $request->observaciones,
            ]);

            foreach ($deudas as $deuda) {
                PagoIntegralDetalle::create([
                    'pagointegral_id' => $pago->id,
                    'periodo' => $deuda->periodo,
                    'monto' => $deuda->saldo,
                    'concepto' => 'Cuota de Condominio',
                ]);

                $deuda->update([
                    'monto_pagado' => $deuda->monto_original + $deuda->monto_mora,
                    'saldo' => 0,
                    'estatus' => 'C',
                ]);
            }

            return $pago;
        });

        return redirect()->route('financiero.pago-integral.comprobante', $pago)
            ->with('success', 'Pago procesado exitosamente.');
    }

    public function comprobante(PagoIntegral $pago)
    {
        $pago->load(['pagoIntegralDetalles', 'compania']);

        return view('financiero.pago-integral-comprobante', compact('pago'));
    }
}
