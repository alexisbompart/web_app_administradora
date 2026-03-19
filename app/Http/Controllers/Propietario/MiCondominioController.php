<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Propietario;
use App\Models\Financiero\Banco;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use App\Models\Financiero\Fondo;
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
            ->get();

        if ($deudas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron deudas validas para pagar.');
        }

        $montoTotal = $deudas->sum('saldo');
        $primerDeuda = $deudas->first();

        DB::beginTransaction();
        try {
            $pago = CondPago::create([
                'compania_id'       => $primerDeuda->compania_id,
                'edificio_id'       => $primerDeuda->edificio_id,
                'fecha_pago'        => $validated['fecha_pago'],
                'forma_pago'        => $validated['forma_pago'],
                'banco_id'          => $validated['banco_id'],
                'numero_referencia' => $validated['numero_referencia'],
                'monto_total'       => $montoTotal,
                'monto_recibido'    => 0,
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
}
