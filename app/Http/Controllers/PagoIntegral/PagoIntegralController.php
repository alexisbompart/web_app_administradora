<?php

namespace App\Http\Controllers\PagoIntegral;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Condominio\Propietario;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\PagoIntegral;
use App\Models\Financiero\PagoIntegralDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoIntegralController extends Controller
{
    public function index()
    {
        $pagos = PagoIntegral::with(['afilpagointegral.afilapto.apartamento.edificio', 'compania'])
            ->latest()->paginate(15);

        return view('financiero.pago-integral', compact('pagos'));
    }

    public function consultarSaldo(Request $request)
    {
        $user = Auth::user();
        $isCliente = $user->hasRole('cliente-propietario');

        $afiliado = null;
        $deudas = collect();
        $afiliados = collect();

        if ($isCliente) {
            $propietario = Propietario::where('user_id', $user->id)->first();
            if ($propietario) {
                $apartamentoIds = $propietario->apartamentos()
                    ->wherePivot('propietario_actual', true)->pluck('cond_aptos.id');
                $afilAptoIds = Afilapto::whereIn('apartamento_id', $apartamentoIds)
                    ->where('estatus_afil', 'A')->pluck('id');
                $afiliados = Afilpagointegral::whereIn('afilapto_id', $afilAptoIds)
                    ->where('estatus', 'A')
                    ->with('afilapto.apartamento.edificio')
                    ->get();
            }
        } else {
            $afiliados = Afilpagointegral::where('estatus', 'A')
                ->with('afilapto.apartamento.edificio')
                ->orderBy('nombres')->get();
        }

        if ($request->filled('afiliado_id')) {
            $afiliado = Afilpagointegral::with('afilapto.apartamento.edificio')->find($request->afiliado_id);
            if ($afiliado && $afiliado->afilapto) {
                $deudas = CondDeudaApto::where('apartamento_id', $afiliado->afilapto->apartamento_id)
                    ->where(function ($q) {
                        $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
                    })
                    ->where(function ($q) {
                        $q->whereNull('serial')->orWhere('serial', 'N');
                    })
                    ->orderBy('periodo')
                    ->get();
            }
        }

        return view('financiero.pago-integral-saldo', compact('afiliados', 'afiliado', 'deudas'));
    }

    public function procesarPago(Request $request)
    {
        if (!$request->has('confirmar')) {
            $request->validate([
                'afiliado_id' => 'required|exists:afilpagointegral,id',
                'deudas' => 'required|array|min:1',
                'deudas.*' => 'exists:cond_deudas_apto,id',
            ]);

            $afiliado = Afilpagointegral::with('afilapto.apartamento.edificio')->findOrFail($request->afiliado_id);

            $allDeudas = CondDeudaApto::where('apartamento_id', $afiliado->afilapto->apartamento_id)
                ->where(function ($q) { $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01'); })
                ->where(function ($q) { $q->whereNull('serial')->orWhere('serial', 'N'); })
                ->orderBy('periodo')->get();

            $selectedIds = collect($request->deudas)->map(fn($id) => (int) $id);

            // Validate consecutive from oldest
            $selecting = true;
            foreach ($allDeudas as $deuda) {
                if ($selecting && !$selectedIds->contains($deuda->id)) {
                    $selecting = false;
                } elseif (!$selecting && $selectedIds->contains($deuda->id)) {
                    return back()->with('error', 'Debe seleccionar deudas consecutivas desde la mas antigua.');
                }
            }

            // Cannot pay ONLY the most recent debt
            if ($allDeudas->count() > 1 && $selectedIds->count() === 1 && $selectedIds->first() === $allDeudas->last()->id) {
                return back()->with('error', 'No puede pagar unicamente la deuda mas reciente.');
            }

            $deudas = CondDeudaApto::whereIn('id', $selectedIds->toArray())->orderBy('periodo')->get();
            $total = $deudas->sum('saldo');

            return view('financiero.pago-integral-procesar', compact('afiliado', 'deudas', 'total'));
        }

        // Confirm payment
        $request->validate([
            'afiliado_id' => 'required|exists:afilpagointegral,id',
            'deudas' => 'required|array|min:1',
            'forma_pago' => 'required|string',
            'referencia' => 'required|string|max:100',
        ]);

        $afiliado = Afilpagointegral::with('afilapto.apartamento.edificio')->findOrFail($request->afiliado_id);
        $deudas = CondDeudaApto::whereIn('id', $request->deudas)->orderBy('periodo')->get();

        if ($deudas->isEmpty()) {
            return redirect()->route('financiero.pago-integral.consultar-saldo')->with('error', 'Sin deudas.');
        }

        $total = $deudas->sum('saldo');

        $pago = DB::transaction(function () use ($request, $afiliado, $deudas, $total) {
            $pago = PagoIntegral::create([
                'afilpagointegral_id' => $afiliado->id,
                'compania_id' => $afiliado->afilapto->compania_id ?? null,
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
                    'concepto' => 'Pago Integral - ' . $deuda->periodo,
                ]);
            }

            return $pago;
        });

        return redirect()->route('financiero.pago-integral.comprobante', $pago)
            ->with('success', 'Pago registrado por ' . number_format($total, 2, ',', '.') . ' Bs.');
    }

    public function comprobante(PagoIntegral $pago)
    {
        $pago->load(['pagoIntegralDetalles', 'compania', 'afilpagointegral.afilapto.apartamento.edificio']);
        return view('financiero.pago-integral-comprobante', compact('pago'));
    }
}
