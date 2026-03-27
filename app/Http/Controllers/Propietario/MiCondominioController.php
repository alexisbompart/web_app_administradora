<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Propietario;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Financiero\Banco;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondMovFactApto;
use App\Models\Financiero\CondMovFactEdif;
use App\Models\Financiero\CondMovPrefact;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use App\Models\Financiero\CondGasto;
use App\Models\Financiero\Fondo;
use App\Models\Financiero\PagoIntegral;
use App\Models\Financiero\PagoIntegralDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MiCondominioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getPropietario()
    {
        return Propietario::where('user_id', Auth::id())->first();
    }

    private function getApartamentos(Propietario $propietario)
    {
        return $propietario->apartamentos()
            ->wherePivot('propietario_actual', true)
            ->with('edificio.compania')
            ->get();
    }

    public function dashboard()
    {
        $propietario = $this->getPropietario();

        if (!$propietario) {
            return view('propietario.sin-acceso');
        }

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $deudasPendientes = CondDeudaApto::whereIn('apartamento_id', $apartamentoIds)
            ->where('estatus', 'P')
            ->get();

        $totalDeuda = $deudasPendientes->sum('saldo');
        $totalMeses = $deudasPendientes->count();

        $pagosRecientes = CondPagoApto::whereIn('apartamento_id', $apartamentoIds)
            ->with('pago')
            ->latest()
            ->take(5)
            ->get();

        $deudasPorApto = $deudasPendientes->groupBy('apartamento_id');

        return view('propietario.dashboard', compact(
            'propietario',
            'apartamentos',
            'deudasPendientes',
            'totalDeuda',
            'totalMeses',
            'pagosRecientes',
            'deudasPorApto'
        ));
    }

    public function deudas(Request $request)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $query = CondDeudaApto::whereIn('apartamento_id', $apartamentoIds)
            ->with(['apartamento.edificio']);

        if ($request->filled('apartamento')) {
            $query->where('apartamento_id', $request->apartamento);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        $deudas = $query->orderByDesc('periodo')->paginate(15)->withQueryString();

        return view('propietario.deudas', compact('propietario', 'apartamentos', 'deudas'));
    }

    public function pagos(Request $request)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $query = CondPagoApto::whereIn('apartamento_id', $apartamentoIds)
            ->with(['pago.banco', 'apartamento.edificio']);

        if ($request->filled('apartamento')) {
            $query->where('apartamento_id', $request->apartamento);
        }

        $pagos = $query->latest()->paginate(15)->withQueryString();

        return view('propietario.pagos', compact('propietario', 'apartamentos', 'pagos'));
    }

    public function recibo($pagoAptoId)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentoIds = $this->getApartamentos($propietario)->pluck('id');

        $pagoApto = CondPagoApto::where('id', $pagoAptoId)
            ->whereIn('apartamento_id', $apartamentoIds)
            ->with(['pago.banco', 'pago.edificio', 'apartamento.edificio.compania', 'deuda'])
            ->firstOrFail();

        if (($pagoApto->pago->estatus ?? 'P') !== 'A') {
            return back()->with('error', 'El recibo no está disponible hasta que el pago sea aprobado.');
        }

        return view('propietario.recibo', compact('propietario', 'pagoApto'));
    }

    public function reciboCondominio($deudaId)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentoIds = $this->getApartamentos($propietario)->pluck('id');

        $deuda = CondDeudaApto::where('id', $deudaId)
            ->whereIn('apartamento_id', $apartamentoIds)
            ->with(['apartamento.edificio.compania', 'edificio'])
            ->firstOrFail();

        return view('propietario.recibo-condominio', compact('propietario', 'deuda'));
    }

    public function estadisticas()
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $stats = [];
        foreach ($apartamentos as $apto) {
            $edificio = $apto->edificio;
            $totalAptosEdificio = Apartamento::where('edificio_id', $edificio->id)->count();

            $deudasEdificio = CondDeudaApto::where('edificio_id', $edificio->id)
                ->where('estatus', 'P')
                ->count();

            $morosidadEdificio = $totalAptosEdificio > 0
                ? round(CondDeudaApto::where('edificio_id', $edificio->id)
                    ->where('estatus', 'P')
                    ->distinct('apartamento_id')
                    ->count('apartamento_id') / $totalAptosEdificio * 100, 1)
                : 0;

            $fondos = Fondo::where('compania_id', $edificio->compania_id)->get();

            $pagosMensuales = CondPagoApto::where('apartamento_id', $apto->id)
                ->with('pago')
                ->whereHas('pago', fn($q) => $q->whereYear('fecha_pago', now()->year))
                ->get()
                ->groupBy(fn($item) => $item->pago?->fecha_pago?->format('Y-m'))
                ->map(fn($group, $key) => [
                    'mes' => \Carbon\Carbon::parse($key . '-01')->translatedFormat('M Y'),
                    'total' => $group->sum('monto_aplicado'),
                ])
                ->values()
                ->toArray();

            $stats[] = [
                'edificio_nombre' => $edificio->nombre,
                'total_aptos' => $totalAptosEdificio,
                'deudas_pendientes' => $deudasEdificio,
                'porcentaje_morosidad' => $morosidadEdificio,
                'num_apto' => $apto->num_apto,
                'alicuota' => $apto->alicuota ?? 0,
                'area' => $apto->area_mts ?? 0,
                'fondos' => $fondos->map(fn($f) => [
                    'nombre' => $f->nombre,
                    'tipo' => $f->tipo,
                    'saldo' => $f->saldo_actual ?? 0,
                ])->toArray(),
                'pagos_mensuales' => $pagosMensuales,
            ];
        }

        return view('propietario.estadisticas', compact('propietario', 'apartamentos', 'stats'));
    }

    public function registrarPagoForm()
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $deudasPendientes = CondDeudaApto::whereIn('apartamento_id', $apartamentoIds)
            ->where('estatus', 'P')
            ->with(['apartamento.edificio'])
            ->orderBy('periodo')
            ->get();

        $bancos = Banco::orderBy('nombre')->get();

        return view('propietario.registrar-pago', compact(
            'propietario', 'apartamentos', 'deudasPendientes', 'bancos'
        ));
    }

    public function registrarPago(Request $request)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $validated = $request->validate([
            'forma_pago'        => 'required|string|in:transferencia,deposito',
            'banco_id'          => 'required|exists:bancos,id',
            'fecha_pago'        => 'required|date|before_or_equal:today',
            'numero_referencia' => 'required|string|max:100',
            'deudas'            => 'required|array|min:1',
            'deudas.*'          => 'exists:cond_deudas_apto,id',
        ]);

        $apartamentoIds = $this->getApartamentos($propietario)->pluck('id');

        $deudas = CondDeudaApto::whereIn('id', $validated['deudas'])
            ->whereIn('apartamento_id', $apartamentoIds)
            ->where('estatus', 'P')
            ->with('apartamento.edificio')
            ->orderBy('periodo')
            ->get();

        if ($deudas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron deudas validas para pagar.');
        }

        // Validate consecutive order from oldest
        $allDeudas = CondDeudaApto::whereIn('apartamento_id', $apartamentoIds)
            ->where('estatus', 'P')
            ->orderBy('periodo')
            ->pluck('id')
            ->toArray();

        $selectedIds = $deudas->pluck('id')->toArray();
        $selecting = true;
        foreach ($allDeudas as $deudaId) {
            if ($selecting && !in_array($deudaId, $selectedIds)) {
                $selecting = false;
            } elseif (!$selecting && in_array($deudaId, $selectedIds)) {
                return redirect()->back()->with('error', 'Debe pagar las deudas en orden cronologico, desde la mas antigua.');
            }
        }

        $montoTotal = $deudas->sum('saldo');
        $primerDeuda = $deudas->first();

        DB::beginTransaction();
        try {
            // Capture BCV rate at payment time
            $tasaBcv = \App\Models\Catalogo\TasaBcv::where('moneda', 'USD')
                ->where('fecha', '<=', now()->toDateString())
                ->orderByDesc('fecha')->first();

            $pago = CondPago::create([
                'compania_id'       => $primerDeuda->compania_id,
                'edificio_id'       => $primerDeuda->edificio_id,
                'fecha_pago'        => $validated['fecha_pago'],
                'forma_pago'        => $validated['forma_pago'],
                'banco_id'          => $validated['banco_id'],
                'numero_referencia' => $validated['numero_referencia'],
                'monto_total'       => $montoTotal,
                'monto_recibido'    => 0,
                'tasa_bcv_pago'     => $tasaBcv?->tasa,
                'estatus'           => 'P',
                'registrado_por'    => Auth::id(),
                'observaciones'     => 'Pago registrado por propietario desde portal web.',
            ]);

            foreach ($deudas as $deuda) {
                CondPagoApto::create([
                    'pago_id'        => $pago->id,
                    'apartamento_id' => $deuda->apartamento_id,
                    'deuda_id'       => $deuda->id,
                    'periodo'        => $deuda->periodo,
                    'monto_aplicado' => $deuda->saldo,
                ]);
            }

            DB::commit();

            return redirect()->route('mi-condominio.pagos')
                ->with('success', 'Pago registrado exitosamente por ' . number_format($montoTotal, 2, ',', '.') . ' Bs. Queda pendiente de aprobacion por el administrador.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar el pago. Intente nuevamente.');
        }
    }

    public function verRecibo($factAptoId)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentoIds = $this->getApartamentos($propietario)->pluck('id');

        $factApto = CondMovFactApto::where('id', $factAptoId)
            ->whereIn('apartamento_id', $apartamentoIds)
            ->with(['edificio.compania', 'apartamento'])
            ->firstOrFail();

        // Get building-level data for same edificio and period
        $factEdif = CondMovFactEdif::where('edificio_id', $factApto->edificio_id)
            ->where('periodo', $factApto->periodo)
            ->first();

        // Get expense breakdown (relacion de cobro) for same edificio and period
        $gastos = CondMovPrefact::where('edificio_id', $factApto->edificio_id)
            ->where('periodo', $factApto->periodo)
            ->where(function ($q) use ($factApto) {
                $q->whereNull('apartamento_id')
                  ->orWhere('apartamento_id', $factApto->apartamento_id)
                  ->orWhere('num_apto_legacy', '0');
            })
            ->orderBy('cod_gasto_legacy')
            ->get();

        // Get payment status from cond_pago_aptos
        $pagoApto = CondPagoApto::where('apartamento_id', $factApto->apartamento_id)
            ->where('periodo', $factApto->periodo)
            ->with('pago')
            ->first();

        $gastoCatalog = $this->buildGastoCatalog($gastos);

        return view('propietario.ver-recibo', compact('propietario', 'factApto', 'factEdif', 'gastos', 'pagoApto', 'gastoCatalog'));
    }

    public function recibosEdificio()
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $edificioIds = $apartamentos->pluck('edificio_id')->unique();

        $recibos = CondMovFactEdif::whereIn('edificio_id', $edificioIds)
            ->with('edificio')
            ->latest()
            ->paginate(15);

        return view('propietario.recibos-edificio', compact('propietario', 'recibos', 'apartamentos'));
    }

    public function verReciboEdificio($factEdifId)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $edificioIds = $this->getApartamentos($propietario)->pluck('edificio_id')->unique();

        $factEdif = CondMovFactEdif::where('id', $factEdifId)
            ->whereIn('edificio_id', $edificioIds)
            ->with(['edificio.compania'])
            ->firstOrFail();

        $gastosRaw = CondMovPrefact::where('edificio_id', $factEdif->edificio_id)
            ->where('periodo', $factEdif->periodo)
            ->orderBy('cod_gasto_legacy')
            ->get();

        $gastoCatalog = $this->buildGastoCatalog($gastosRaw);

        // Group by cod_gasto_legacy and sum montos
        $gastos = $gastosRaw->groupBy('cod_gasto_legacy')->map(function ($rows, $cod) use ($gastoCatalog) {
            $first = $rows->first();
            $desc  = $first->concepto;
            if (!$desc || $desc == $cod) {
                $desc = $gastoCatalog[$cod . '|' . $first->tipo_gasto_legacy]
                     ?? $gastoCatalog[$cod]
                     ?? $first->ext_descripcion
                     ?? $cod;
            }
            return [
                'cod_gasto_legacy' => $cod,
                'descripcion'      => $desc,
                'ampl_concepto'    => $rows->firstWhere('ampl_concepto', '!=', null)?->ampl_concepto ?? '',
                'monto'            => $rows->sum('monto'),
            ];
        })->sortBy('cod_gasto_legacy')->values();

        return view('propietario.ver-recibo-edificio', compact('propietario', 'factEdif', 'gastos'));
    }

    /**
     * Build a description catalog from cond_gastos keyed by "cod_gasto|tipo_gasto".
     * Falls back to tipo_gasto=0 if exact match not found.
     */
    private function buildGastoCatalog($prefactRows): array
    {
        $codigos = $prefactRows->pluck('cod_gasto_legacy')->unique()->filter()->values();
        if ($codigos->isEmpty()) return [];

        $catalog = [];
        CondGasto::whereIn('cod_gasto', $codigos)
            ->get(['cod_gasto', 'tipo_gasto', 'descripcion'])
            ->each(function ($g) use (&$catalog) {
                $catalog[$g->cod_gasto . '|' . $g->tipo_gasto] = $g->descripcion;
                // Also store base key (tipo=0) for fallback
                if (!isset($catalog[$g->cod_gasto]) || $g->tipo_gasto == '0') {
                    $catalog[$g->cod_gasto] = $g->descripcion;
                }
            });

        return $catalog;
    }

    public function recibosApartamento()
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        $recibos = CondMovFactApto::whereIn('apartamento_id', $apartamentoIds)
            ->with(['edificio', 'apartamento'])
            ->latest()
            ->paginate(15);

        return view('propietario.recibos-apartamento', compact('propietario', 'recibos', 'apartamentos'));
    }

    public function pagoIntegralForm()
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        // 1) Search by apartment chain: apartamento → afilapto → afilpagointegral
        $afiliado = null;
        if ($apartamentoIds->isNotEmpty()) {
            $afilAptoIds = Afilapto::whereIn('apartamento_id', $apartamentoIds)
                ->where('estatus_afil', 'A')->pluck('id');

            $afiliado = Afilpagointegral::whereIn('afilapto_id', $afilAptoIds)
                ->where('estatus', 'A')
                ->with('afilapto.apartamento.edificio')
                ->first();
        }

        // 2) Fallback: search by propietario cedula in afilpagointegral
        if (!$afiliado && $propietario->cedula) {
            $cedulaLimpia = preg_replace('/[^0-9]/', '', $propietario->cedula);
            $afiliado = Afilpagointegral::where('estatus', 'A')
                ->where(function ($q) use ($cedulaLimpia) {
                    $q->where('cedula_rif', $cedulaLimpia)
                      ->orWhere('cedula_rif', 'LIKE', "%-{$cedulaLimpia}")
                      ->orWhere(DB::raw("REPLACE(REPLACE(cedula_rif, '.', ''), '-', '')"), $cedulaLimpia);
                })
                ->with('afilapto.apartamento.edificio')
                ->first();
        }

        $deudas = collect();
        if ($afiliado && $afiliado->afilapto) {
            $deudas = CondDeudaApto::where('apartamento_id', $afiliado->afilapto->apartamento_id)
                ->where('estatus', 'P')
                ->orderBy('periodo')
                ->get();
        }

        // Pagos integrales previos del propietario
        $pagosIntegrales = collect();
        if ($afiliado) {
            $pagosIntegrales = PagoIntegral::where('afilpagointegral_id', $afiliado->id)
                ->with('pagoIntegralDetalles')
                ->latest('fecha')
                ->take(10)
                ->get();
        }

        return view('propietario.pago-integral', compact(
            'propietario', 'apartamentos', 'afiliado', 'deudas', 'pagosIntegrales'
        ));
    }

    public function pagoIntegralStore(Request $request)
    {
        $propietario = $this->getPropietario();
        if (!$propietario) return view('propietario.sin-acceso');

        $request->validate([
            'afiliado_id' => 'required|exists:afilpagointegral,id',
            'deudas'      => 'required|array|min:1',
            'deudas.*'    => 'exists:cond_deudas_apto,id',
        ]);

        $apartamentos = $this->getApartamentos($propietario);
        $apartamentoIds = $apartamentos->pluck('id');

        // Verify the afiliado belongs to this propietario
        $afiliado = Afilpagointegral::with('afilapto.apartamento')
            ->where('id', $request->afiliado_id)
            ->where('estatus', 'A')
            ->firstOrFail();

        if (!$afiliado->afilapto || !$apartamentoIds->contains($afiliado->afilapto->apartamento_id)) {
            return back()->with('error', 'El afiliado no corresponde a sus apartamentos.');
        }

        $apartamentoId = $afiliado->afilapto->apartamento_id;

        // Get all pending debts for validation
        $allDeudas = CondDeudaApto::where('apartamento_id', $apartamentoId)
            ->where('estatus', 'P')
            ->orderBy('periodo')
            ->get();

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

        $deudas = CondDeudaApto::whereIn('id', $selectedIds->toArray())
            ->where('apartamento_id', $apartamentoId)
            ->where('estatus', 'P')
            ->orderBy('periodo')
            ->get();

        if ($deudas->isEmpty()) {
            return back()->with('error', 'No se encontraron deudas validas.');
        }

        $total = $deudas->sum('saldo');

        $pago = DB::transaction(function () use ($afiliado, $deudas, $total) {
            $pago = PagoIntegral::create([
                'afilpagointegral_id' => $afiliado->id,
                'compania_id'         => $afiliado->afilapto->compania_id ?? null,
                'fecha'               => now(),
                'monto_total'         => $total,
                'forma_pago'          => 'pago_integral',
                'referencia'          => null,
                'estatus'             => 'P',
                'observaciones'       => 'Registrado por propietario desde portal web.',
            ]);

            foreach ($deudas as $deuda) {
                PagoIntegralDetalle::create([
                    'pagointegral_id' => $pago->id,
                    'periodo'         => $deuda->periodo,
                    'monto'           => $deuda->saldo,
                    'concepto'        => 'Pago Integral - ' . $deuda->periodo,
                ]);
            }

            return $pago;
        });

        return redirect()->route('mi-condominio.pago-integral')
            ->with('success', 'Pago integral registrado por ' . number_format($total, 2, ',', '.') . ' Bs. Queda pendiente de aprobacion por el administrador.');
    }
}
