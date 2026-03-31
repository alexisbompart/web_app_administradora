<?php

namespace App\Http\Controllers\Informe;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondGasto;
use App\Models\Financiero\CondPago;
use App\Models\Financiero\CondPagoApto;
use App\Models\InformeComunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:informes.ver')->only([
            'index', 'estadoCuenta', 'morosos', 'relacionGastos',
            'informeAnual', 'planOperativo', 'circulares',
        ]);
        $this->middleware('permission:informes.generar')->only(['generarInforme', 'enviarCircular']);
    }

    public function index()
    {
        $informes = InformeComunidad::latest()->paginate(10);

        return view('informes.index', compact('informes'));
    }

    public function estadoCuenta(Request $request)
    {
        $edificios = Edificio::with('compania')->orderBy('nombre')->get();
        $apartamentos = Apartamento::with('edificio')->orderBy('num_apto')->get();

        $apartamento = null;
        $deudas = collect();
        $pagos = collect();

        if ($request->filled('apartamento_id')) {
            $apartamento = Apartamento::with('edificio')->find($request->apartamento_id);

            if ($apartamento) {
                $deudasQuery = CondDeudaApto::where('apartamento_id', $apartamento->id);

                if ($request->filled('periodo_desde')) {
                    $deudasQuery->where('periodo', '>=', $request->periodo_desde);
                }
                if ($request->filled('periodo_hasta')) {
                    $deudasQuery->where('periodo', '<=', $request->periodo_hasta);
                }

                $deudas = $deudasQuery->orderBy('periodo', 'desc')->get();

                $pagos = CondPagoApto::where('apartamento_id', $apartamento->id)
                    ->with('condPago')
                    ->orderByDesc('id')
                    ->get();
            }
        }

        return view('informes.estado-cuenta', compact(
            'edificios',
            'apartamentos',
            'apartamento',
            'deudas',
            'pagos'
        ));
    }

    public function morosos(Request $request)
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $mesesMinimos = (int) $request->input('meses', 1);

        $query = CondDeudaApto::pendientes()
            ->select(
                'apartamento_id',
                DB::raw('COUNT(*) as meses_vencidos'),
                DB::raw('SUM(saldo) as total_deuda')
            )
            ->groupBy('apartamento_id')
            ->having(DB::raw('COUNT(*)'), '>=', $mesesMinimos);

        if ($request->filled('edificio_id')) {
            $query->where('edificio_id', $request->edificio_id);
        }

        $morosrosRaw = $query->get();

        $morosos = $morosrosRaw->map(function ($item) {
            $apartamento = Apartamento::with('edificio')->find($item->apartamento_id);
            if (!$apartamento) {
                return null;
            }

            $ultimoPago = CondPagoApto::where('apartamento_id', $item->apartamento_id)
                ->with('condPago')
                ->latest('id')
                ->first();

            return (object) [
                'apartamento_id' => $item->apartamento_id,
                'num_apto' => $apartamento->num_apto,
                'propietario_nombre' => $apartamento->propietario_nombre ?? 'N/A',
                'edificio_nombre' => $apartamento->edificio->nombre ?? 'N/A',
                'meses_vencidos' => $item->meses_vencidos,
                'total_deuda' => $item->total_deuda,
                'ultimo_pago' => $ultimoPago?->condPago?->fecha_pago?->format('d/m/Y'),
            ];
        })->filter();

        return view('informes.morosos', compact('morosos', 'edificios'));
    }

    public function generarInforme(Request $request)
    {
        $informe = InformeComunidad::create($request->all());

        return response()->json([
            'message' => 'Informe generado exitosamente',
            'module'  => 'Informe',
            'data'    => $informe,
        ], 201);
    }

    public function relacionGastos(Request $request)
    {
        $edificios = Edificio::with('compania')->orderBy('nombre')->get();
        $edificio = null;
        $gastos = collect();
        $periodo = $request->input('periodo', now()->format('Y-m'));

        if ($request->filled('edificio_id')) {
            $edificio = Edificio::with('compania')->find($request->edificio_id);

            if ($edificio) {
                $gastos = CondGasto::where('edificio_id', $edificio->id)
                    ->where('activo', true)
                    ->orderBy('tipo')
                    ->orderBy('descripcion')
                    ->get();
            }
        }

        return view('informes.relacion-gastos', compact('edificios', 'edificio', 'gastos', 'periodo'));
    }

    public function informeAnual(Request $request)
    {
        $edificios = Edificio::with('compania')->orderBy('nombre')->get();
        $edificio = null;
        $anio = (int) $request->input('anio', now()->year);
        $data = [];

        if ($request->filled('edificio_id')) {
            $edificio = Edificio::with('compania')->find($request->edificio_id);

            if ($edificio) {
                // Ingresos por mes (pagos recibidos)
                $ingresosPorMes = CondPago::whereYear('fecha_pago', $anio)
                    ->select(
                        DB::raw('EXTRACT(MONTH FROM fecha_pago) as mes'),
                        DB::raw('SUM(monto_total) as total')
                    )
                    ->groupBy(DB::raw('EXTRACT(MONTH FROM fecha_pago)'))
                    ->orderBy(DB::raw('EXTRACT(MONTH FROM fecha_pago)'))
                    ->pluck('total', 'mes')
                    ->toArray();

                // Gastos del edificio
                $gastos = CondGasto::where('edificio_id', $edificio->id)
                    ->where('activo', true)
                    ->get();

                $totalGastosMensual = $gastos->sum('monto_base');

                // Deudas pendientes
                $deudasPendientes = CondDeudaApto::where('edificio_id', $edificio->id)
                    ->pendientes()
                    ->sum('saldo');

                // Deudas cobradas en el año
                $deudasCobradas = CondDeudaApto::where('edificio_id', $edificio->id)
                    ->where(function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNotNull('serial')->where('serial', '!=', 'N');
                        });
                    })
                    ->whereYear('updated_at', $anio)
                    ->sum('monto_original');

                // Morosos
                $morososCount = CondDeudaApto::where('edificio_id', $edificio->id)
                    ->pendientes()
                    ->select('apartamento_id')
                    ->groupBy('apartamento_id')
                    ->having(DB::raw('COUNT(*)'), '>=', 2)
                    ->get()
                    ->count();

                // Total apartamentos
                $totalAptos = Apartamento::where('edificio_id', $edificio->id)->count();

                $data = [
                    'ingresos_por_mes' => $ingresosPorMes,
                    'total_gastos_mensual' => $totalGastosMensual,
                    'total_gastos_anual' => $totalGastosMensual * 12,
                    'deudas_pendientes' => $deudasPendientes,
                    'deudas_cobradas' => $deudasCobradas,
                    'morosos_count' => $morososCount,
                    'total_aptos' => $totalAptos,
                    'gastos' => $gastos,
                ];
            }
        }

        return view('informes.informe-anual', compact('edificios', 'edificio', 'anio', 'data'));
    }

    public function planOperativo(Request $request)
    {
        $edificios = Edificio::with('compania')->orderBy('nombre')->get();
        $edificio = null;
        $anio = (int) $request->input('anio', now()->year);
        $data = [];

        if ($request->filled('edificio_id')) {
            $edificio = Edificio::with('compania')->find($request->edificio_id);

            if ($edificio) {
                // Gastos actuales del edificio
                $gastos = CondGasto::where('edificio_id', $edificio->id)
                    ->where('activo', true)
                    ->orderBy('tipo')
                    ->orderBy('descripcion')
                    ->get();

                $totalGastosMensual = $gastos->sum('monto_base');
                $totalAptos = Apartamento::where('edificio_id', $edificio->id)->count();

                // Deudas pendientes
                $deudasPendientes = CondDeudaApto::where('edificio_id', $edificio->id)
                    ->pendientes()
                    ->sum('saldo');

                $data = [
                    'gastos' => $gastos,
                    'total_gastos_mensual' => $totalGastosMensual,
                    'total_gastos_anual' => $totalGastosMensual * 12,
                    'total_aptos' => $totalAptos,
                    'cuota_estimada' => $totalAptos > 0 ? $totalGastosMensual / $totalAptos : 0,
                    'deudas_pendientes' => $deudasPendientes,
                ];
            }
        }

        return view('informes.plan-operativo', compact('edificios', 'edificio', 'anio', 'data'));
    }

    public function circulares(Request $request)
    {
        $edificios = Edificio::orderBy('nombre')->get();

        $circulares = InformeComunidad::where('tipo', 'circular')
            ->latest()
            ->paginate(10);

        return view('informes.circulares', compact('edificios', 'circulares'));
    }

    public function enviarCircular(Request $request)
    {
        $validated = $request->validate([
            'edificio_id' => 'required|exists:edificios,id',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $edificio = Edificio::find($validated['edificio_id']);

        $circular = InformeComunidad::create([
            'compania_id'      => $edificio->compania_id,
            'edificio_id'      => $validated['edificio_id'],
            'generado_por'     => auth()->id(),
            'tipo'             => 'circular',
            'titulo'           => $validated['titulo'],
            'contenido'        => $validated['descripcion'],
            'fecha_generacion' => now(),
            'enviado'          => true,
            'fecha_envio'      => now(),
        ]);

        return redirect()->route('servicios.informes.circulares')
            ->with('success', 'Circular publicada exitosamente.');
    }
}
