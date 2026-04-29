<?php

namespace App\Http\Controllers\PagoIntegral;

use App\Http\Controllers\Controller;
use App\Models\Catalogo\Estado;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Propietario;
use App\Models\Financiero\Banco;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\PagoIntegral;
use App\Models\Financiero\PagoIntegralArchivo;
use App\Models\Financiero\PagoIntegralDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoIntegralController extends Controller
{
    public function index()
    {
        $pendientes = PagoIntegral::where('estatus', 'P')
            ->with(['afilpagointegral.banco', 'afilpagointegral.afilaptos.apartamento.edificio', 'compania', 'pagoIntegralDetalles'])
            ->latest('fecha')->get();

        // Agrupar pendientes por banco
        $pendientesPorBanco = $pendientes->groupBy(fn($p) => $p->afilpagointegral?->banco_id ?? 0);
        $bancosPendientes = Banco::whereIn('id', $pendientesPorBanco->keys()->filter())->orderBy('nombre')->get()->keyBy('id');

        $procesados = PagoIntegral::whereIn('estatus', ['A', 'R'])
            ->with(['afilpagointegral.banco', 'afilpagointegral.afilaptos.apartamento.edificio', 'compania', 'pagoIntegralDetalles'])
            ->latest('fecha')->paginate(15);

        $totalPendiente = PagoIntegral::where('estatus', 'P')->sum('monto_total');
        $countPendiente = PagoIntegral::where('estatus', 'P')->count();
        $countAprobados = PagoIntegral::where('estatus', 'A')->count();
        $countRechazados = PagoIntegral::where('estatus', 'R')->count();

        return view('financiero.pago-integral', compact(
            'pendientesPorBanco', 'bancosPendientes', 'procesados', 'totalPendiente', 'countPendiente', 'countAprobados', 'countRechazados'
        ));
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
                $afilpagointegralIds = Afilapto::whereIn('apartamento_id', $apartamentoIds)
                    ->where('estatus_afil', 'A')
                    ->whereNotNull('afilpagointegral_id')
                    ->pluck('afilpagointegral_id');
                $afiliados = Afilpagointegral::whereIn('id', $afilpagointegralIds)
                    ->where('estatus', 'A')
                    ->with('afilaptos.apartamento.edificio')
                    ->get();
            }
        } else {
            $afiliados = Afilpagointegral::where('estatus', 'A')
                ->with('afilaptos.apartamento.edificio')
                ->orderBy('nombres')->get();
        }

        if ($request->filled('afiliado_id')) {
            $afiliado = Afilpagointegral::with('afilaptos.apartamento.edificio')->find($request->afiliado_id);
            if ($afiliado) {
                $primerApto = $afiliado->afilaptos->first();
                if ($primerApto) {
                    $deudas = CondDeudaApto::where('apartamento_id', $primerApto->apartamento_id)
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

            $afiliado = Afilpagointegral::with('afilaptos.apartamento.edificio')->findOrFail($request->afiliado_id);
            $primerApto = $afiliado->afilaptos->first();

            $allDeudas = CondDeudaApto::where('apartamento_id', $primerApto?->apartamento_id)
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

        $afiliado = Afilpagointegral::with('afilaptos.apartamento.edificio')->findOrFail($request->afiliado_id);
        $deudas = CondDeudaApto::whereIn('id', $request->deudas)->orderBy('periodo')->get();

        if ($deudas->isEmpty()) {
            return redirect()->route('financiero.pago-integral.consultar-saldo')->with('error', 'Sin deudas.');
        }

        $total = $deudas->sum('saldo');

        $pago = DB::transaction(function () use ($request, $afiliado, $deudas, $total) {
            $pago = PagoIntegral::create([
                'afilpagointegral_id' => $afiliado->id,
                'compania_id' => $afiliado->afilaptos->first()?->compania_id ?? null,
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
        $pago->load(['pagoIntegralDetalles', 'compania', 'afilpagointegral.afilaptos.apartamento.edificio']);
        return view('financiero.pago-integral-comprobante', compact('pago'));
    }

    public function aprobarPago(PagoIntegral $pago)
    {
        if ($pago->estatus !== 'P') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        DB::beginTransaction();
        try {
            $pago->update([
                'estatus' => 'A',
                'observaciones' => trim(($pago->observaciones ?? '') . ' | Aprobado por ' . Auth::user()->name . ' el ' . now()->format('d/m/Y H:i')),
            ]);

            // Mark related debts as cancelled using the detalles periods
            if ($pago->afilpagointegral && $pago->afilpagointegral->afilaptos->isNotEmpty()) {
                $apartamentoId = $pago->afilpagointegral->afilaptos->first()->apartamento_id;
                $periodos = $pago->pagoIntegralDetalles->pluck('periodo')->toArray();

                CondDeudaApto::where('apartamento_id', $apartamentoId)
                    ->whereIn('periodo', $periodos)
                    ->update([
                        'estatus' => 'C',
                        'monto_pagado' => DB::raw('monto_original'),
                        'saldo' => 0,
                        'fecha_pag' => $pago->fecha,
                    ]);
            }

            DB::commit();
            return back()->with('success', 'Pago Integral #' . $pago->id . ' aprobado correctamente. Las deudas han sido marcadas como canceladas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    public function rechazarPago(Request $request, PagoIntegral $pago)
    {
        $request->validate(['motivo' => 'required|string|max:500']);

        if ($pago->estatus !== 'P') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        $pago->update([
            'estatus' => 'R',
            'observaciones' => trim(($pago->observaciones ?? '') . ' | Rechazado: ' . $request->motivo . ' (' . Auth::user()->name . ' ' . now()->format('d/m/Y') . ')'),
        ]);

        return back()->with('success', 'Pago Integral #' . $pago->id . ' rechazado.');
    }

    public function aprobacion(Request $request)
    {
        $query = PagoIntegral::with([
            'afilpagointegral.banco',
            'afilpagointegral.afilaptos.apartamento',
            'afilpagointegral.afilaptos.edificio',
            'afilpagointegral.afilaptos.compania',
        ]);

        if ($request->filled('cedula')) {
            $cedula = $request->cedula;
            $query->whereHas('afilpagointegral', fn($q) => $q->where('cedula_rif', 'like', "%{$cedula}%"));
        }

        if ($request->filled('pint')) {
            $query->where('id', $request->pint);
        }

        $pagos = $query->orderByDesc('fecha')->paginate(50)->withQueryString();

        return view('financiero.pago-integral-aprobacion', compact('pagos'));
    }

    public function generarArchivoForm()
    {
        $bancos = Banco::orderBy('nombre')->get();
        return view('financiero.pago-integral-generar-archivo', compact('bancos'));
    }

    public function generarArchivo(Request $request)
    {
        $request->validate([
            'banco_id'      => 'required|exists:bancos,id',
            'tipo_archivo'  => 'required|in:PAGOS_ENVIOS,DESAFILIACION,AFILIACIONES_ENVIOS',
        ]);

        $banco = Banco::findOrFail($request->banco_id);

        $pagos = PagoIntegral::where('estatus', 'P')
            ->whereHas('afilpagointegral', fn($q) => $q->where('banco_id', $request->banco_id))
            ->with([
                'afilpagointegral.banco',
                'afilpagointegral.afilaptos.apartamento',
                'afilpagointegral.afilaptos.edificio',
                'afilpagointegral.afilaptos.compania',
                'pagoIntegralDetalles',
            ])
            ->get();

        if ($pagos->isEmpty()) {
            return back()->with('error', 'No hay pagos pendientes para ' . $banco->nombre);
        }

        $content  = $this->generarContenidoArchivo($pagos, $banco, $request->tipo_archivo);
        $filename = $banco->iniciales . 'cobro' . $pagos->count() . '.txt';

        // Registrar archivo generado
        $archivo = PagoIntegralArchivo::create([
            'banco_id'        => $banco->id,
            'nombre_archivo'  => $filename,
            'tipo_archivo'    => $request->tipo_archivo,
            'cantidad_pagos'  => $pagos->count(),
            'monto_total'     => $pagos->sum('monto_total'),
            'estatus'         => PagoIntegralArchivo::ESTATUS_GENERADO,
            'generado_por'    => Auth::id(),
            'fecha_generado'  => now(),
        ]);
        $archivo->pagos()->attach($pagos->pluck('id'));

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/plain']);
    }

    public function archivos()
    {
        $archivos = PagoIntegralArchivo::with(['banco', 'generadoPor'])
            ->latest('fecha_generado')
            ->paginate(20);

        return view('financiero.pago-integral-archivos', compact('archivos'));
    }

    public function archivoDetalle(PagoIntegralArchivo $archivo)
    {
        $archivo->load([
            'banco', 'generadoPor',
            'pagos.afilpagointegral.afilaptos.apartamento.edificio',
            'pagos.pagoIntegralDetalles',
        ]);

        return view('financiero.pago-integral-archivo-detalle', compact('archivo'));
    }

    public function actualizarEstatusArchivo(Request $request, PagoIntegralArchivo $archivo)
    {
        $request->validate([
            'estatus' => 'required|in:GE,EN,EP,PR',
        ]);

        $nuevoEstatus = $request->estatus;
        $data = ['estatus' => $nuevoEstatus];

        if ($nuevoEstatus === PagoIntegralArchivo::ESTATUS_ENVIADO) {
            $data['fecha_enviado'] = now();
        } elseif ($nuevoEstatus === PagoIntegralArchivo::ESTATUS_PROCESADO) {
            $data['fecha_procesado'] = now();

            // Al procesar archivo, aprobar todos los pagos incluidos
            DB::beginTransaction();
            try {
                $archivo->update($data);

                foreach ($archivo->pagos()->where('estatus', 'P')->get() as $pago) {
                    $pago->update([
                        'estatus'       => 'A',
                        'observaciones' => trim(($pago->observaciones ?? '') . ' | Aprobado via archivo #' . $archivo->id . ' (' . Auth::user()->name . ' ' . now()->format('d/m/Y H:i') . ')'),
                    ]);

                    // Cancelar deudas
                    $apartamentoId = $pago->afilpagointegral?->afilaptos->first()?->apartamento_id;
                    $periodos = $pago->pagoIntegralDetalles->pluck('periodo')->toArray();

                    if ($apartamentoId && !empty($periodos)) {
                        CondDeudaApto::where('apartamento_id', $apartamentoId)
                            ->whereIn('periodo', $periodos)
                            ->update([
                                'estatus'      => 'C',
                                'monto_pagado' => DB::raw('monto_original'),
                                'saldo'        => 0,
                                'fecha_pag'    => $pago->fecha,
                            ]);
                    }
                }

                DB::commit();
                return back()->with('success', 'Archivo #' . $archivo->id . ' marcado como Procesado. ' . $archivo->cantidad_pagos . ' pago(s) aprobados.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error al procesar: ' . $e->getMessage());
            }
        }

        if ($nuevoEstatus !== PagoIntegralArchivo::ESTATUS_PROCESADO) {
            $archivo->update($data);
        }

        $label = PagoIntegralArchivo::ESTATUS_LABELS[$nuevoEstatus] ?? $nuevoEstatus;
        return back()->with('success', 'Archivo #' . $archivo->id . ' actualizado a: ' . $label);
    }

    // ============ PROCESAR RESPUESTA BANCARIA ============

    public function procesarRespuestaForm(PagoIntegralArchivo $archivo)
    {
        $archivo->load(['banco', 'pagos.afilpagointegral', 'pagos.pagoIntegralDetalles']);
        return view('financiero.pago-integral-procesar-respuesta', compact('archivo'));
    }

    public function procesarRespuesta(Request $request, PagoIntegralArchivo $archivo)
    {
        $request->validate([
            'archivo_respuesta' => 'required|file|max:10240',
        ]);

        $archivo->load(['banco', 'pagos.afilpagointegral.afilaptos', 'pagos.pagoIntegralDetalles']);

        $content = file_get_contents($request->file('archivo_respuesta')->getRealPath());
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
        $lines = preg_split('/\r\n|\r|\n/', $content);

        $iniciales = strtoupper(trim($archivo->banco->iniciales ?? ''));

        $registros = match ($iniciales) {
            'BC'  => $this->parsearRespuestaBancaribe($lines),
            'BM'  => $this->parsearRespuestaMercantil($lines),
            'BAN' => $this->parsearRespuestaBanesco($lines),
            default => [],
        };

        if (empty($registros)) {
            return back()->with('error', 'No se pudieron leer registros del archivo de respuesta para banco ' . ($archivo->banco->nombre ?? 'desconocido') . '.');
        }

        // Mapear pagos del archivo por cedula
        $pagosPorCedula = [];
        foreach ($archivo->pagos as $pago) {
            $cedula = trim($pago->afilpagointegral?->cedula_rif ?? '');
            if ($cedula) {
                $pagosPorCedula[$cedula][] = $pago;
            }
        }

        $results = ['aprobados' => 0, 'rechazados' => 0, 'no_encontrados' => 0, 'detalles' => []];

        DB::beginTransaction();
        try {
            foreach ($registros as $reg) {
                $cedula = $reg['cedula'];
                $exitoso = $reg['exitoso'];
                $mensaje = $reg['mensaje'];
                $monto = $reg['monto'] ?? null;

                if (!isset($pagosPorCedula[$cedula])) {
                    $results['no_encontrados']++;
                    $results['detalles'][] = ['cedula' => $cedula, 'estatus' => 'No encontrado', 'mensaje' => $mensaje];
                    continue;
                }

                foreach ($pagosPorCedula[$cedula] as $pago) {
                    if ($pago->estatus !== 'P') continue;

                    if ($exitoso) {
                        $pago->update([
                            'estatus' => 'A',
                            'observaciones' => trim(($pago->observaciones ?? '') . ' | Aprobado via respuesta bancaria - ' . $mensaje . ' (' . Auth::user()->name . ' ' . now()->format('d/m/Y H:i') . ')'),
                        ]);

                        $apartamentoId = $pago->afilpagointegral?->afilaptos->first()?->apartamento_id;
                        $periodos = $pago->pagoIntegralDetalles->pluck('periodo')->toArray();

                        if ($apartamentoId && !empty($periodos)) {
                            CondDeudaApto::where('apartamento_id', $apartamentoId)
                                ->whereIn('periodo', $periodos)
                                ->update([
                                    'estatus' => 'C',
                                    'monto_pagado' => DB::raw('monto_original'),
                                    'saldo' => 0,
                                    'fecha_pag' => $pago->fecha ?? now(),
                                ]);
                        }

                        $results['aprobados']++;
                        $results['detalles'][] = ['cedula' => $cedula, 'estatus' => 'Aprobado', 'mensaje' => $mensaje];
                    } else {
                        $pago->update([
                            'estatus' => 'R',
                            'observaciones' => trim(($pago->observaciones ?? '') . ' | Rechazado: ' . $mensaje . ' (' . Auth::user()->name . ' ' . now()->format('d/m/Y H:i') . ')'),
                        ]);

                        $results['rechazados']++;
                        $results['detalles'][] = ['cedula' => $cedula, 'estatus' => 'Rechazado', 'mensaje' => $mensaje];
                    }
                }

                unset($pagosPorCedula[$cedula]);
            }

            // Actualizar estatus del archivo
            $archivo->update([
                'estatus' => PagoIntegralArchivo::ESTATUS_PROCESADO,
                'fecha_procesado' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }

        return view('financiero.pago-integral-procesar-respuesta', compact('archivo', 'results'));
    }

    /**
     * Bancaribe: cedula/cuenta/monto/nombre/periodo/fecha/tipo/mensaje
     */
    private function parsearRespuestaBancaribe(array $lines): array
    {
        $registros = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            $parts = explode('/', $line);
            if (count($parts) < 8) continue;

            $cedulaRaw = $parts[0];
            $cedula = preg_replace('/^[VJEGP]/i', '', $cedulaRaw);
            $monto = (float) str_replace(['.', ','], ['', '.'], $parts[2]);
            $mensaje = trim($parts[7] ?? '');
            $exitoso = stripos($mensaje, 'correctamente') !== false;

            $registros[] = [
                'cedula' => $cedula,
                'monto' => $monto,
                'exitoso' => $exitoso,
                'mensaje' => $mensaje,
            ];
        }
        return $registros;
    }

    /**
     * Mercantil: linea tipo 2 = detalle, contiene cedula, monto, codigo respuesta
     */
    private function parsearRespuestaMercantil(array $lines): array
    {
        $registros = [];
        foreach ($lines as $line) {
            if (strlen($line) < 120 || $line[0] !== '2') continue;

            // Cedula: posicion 2-12 (despues de tipo y letra)
            $letra = $line[1] ?? '';
            $cedulaPadded = substr($line, 2, 9);
            $cedula = ltrim($cedulaPadded, '0');

            // Monto: posicion 68-84 (17 digitos, centavos)
            $montoCents = (int) substr($line, 68, 17);
            $monto = $montoCents / 100;

            // Codigo respuesta: posicion ~140, buscar "0074" (COBRO EXITOSO)
            $codigoResp = substr($line, 139, 4);
            $mensaje = trim(substr($line, 143, 30));
            $exitoso = $codigoResp === '0074';

            $registros[] = [
                'cedula' => $cedula,
                'monto' => $monto,
                'exitoso' => $exitoso,
                'mensaje' => $mensaje ?: ($exitoso ? 'COBRO EXITOSO' : 'RECHAZADO COD:' . $codigoResp),
            ];
        }
        return $registros;
    }

    /**
     * Banesco: lineas 022=detalle, 030=respuesta del detalle anterior
     */
    private function parsearRespuestaBanesco(array $lines): array
    {
        $registros = [];
        $currentCedula = null;
        $currentMonto = null;

        foreach ($lines as $line) {
            $line = rtrim($line);
            if (strlen($line) < 3) continue;

            $tipo = substr($line, 0, 3);

            if ($tipo === '022') {
                // Extraer cedula (buscar V/J seguido de digitos)
                if (preg_match('/[VJEGP](\d+)/', $line, $m)) {
                    $currentCedula = ltrim($m[1], '0');
                }
                // Monto: posicion 39-56 (18 digitos, centavos)
                if (strlen($line) >= 56) {
                    $montoCents = (int) substr($line, 39, 17);
                    $currentMonto = $montoCents / 100;
                }
            } elseif ($tipo === '030' && $currentCedula) {
                $codigoResp = substr($line, 3, 4);
                $mensaje = trim(substr($line, 7));
                $exitoso = $codigoResp === '0074';

                $registros[] = [
                    'cedula' => $currentCedula,
                    'monto' => $currentMonto,
                    'exitoso' => $exitoso,
                    'mensaje' => $mensaje ?: ($exitoso ? 'COBRO EXITOSO' : 'RECHAZADO COD:' . $codigoResp),
                ];

                $currentCedula = null;
                $currentMonto = null;
            }
        }
        return $registros;
    }

    // ============ DATOS EMPRESA POR BANCO ============
    private const EMPRESA_MERCANTIL = [
        'rif'    => 'J0001426434',
        'cuenta' => '01050026521026353807',
    ];
    private const EMPRESA_BANESCO = [
        'rif'       => 'J001426434',
        'nombre'    => 'ADMINISTRADORA INTEGRAL E L B  C A',
        'cuenta'    => '01340277912771010167',
        'servicio'  => '589',
        'cod_banco' => 'BANSVECA',
    ];
    private const EMPRESA_BANCARIBE = [
        'rif'    => 'J315799111',
        'nombre' => 'CASTELLANA HOTEL',
        'cuenta' => '01140165161650069095',
    ];

    private function generarContenidoArchivo($pagos, $banco, $tipo): string
    {
        $iniciales = strtoupper(trim($banco->iniciales ?? ''));

        // ============ BANCARIBE ============
        if (in_array($iniciales, ['BC', 'BANCARIBE'])) {
            return $this->generarArchivoBancaribe($pagos);
        }

        // ============ MERCANTIL ============
        if (in_array($iniciales, ['BM', 'MERCANTIL', 'BAMR'])) {
            return $this->generarArchivoMercantil($pagos);
        }

        // ============ BANESCO ============
        if (in_array($iniciales, ['BB', 'BANESCO', 'BAN'])) {
            return $this->generarArchivoBanesco($pagos);
        }

        // Default: simple CSV
        $lines = ['CEDULA,NOMBRE,CUENTA,MONTO,FECHA'];
        foreach ($pagos as $pago) {
            $afil   = $pago->afilpagointegral;
            $letra  = $afil->letra ?? 'V';
            $nombre = trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? ''));
            $lines[] = implode(',', [
                $letra . ($afil->cedula_rif ?? ''),
                '"' . $nombre . '"',
                $afil->cta_bancaria ?? '',
                number_format((float) $pago->monto_total, 2, '.', ''),
                $pago->fecha->format('d/m/Y'),
            ]);
        }
        return implode("\n", $lines);
    }

    /**
     * Formato Mercantil: Header "1" + Detail "2" por cada pago
     * Basado en modelo real Mcobro.txt
     */
    private function generarArchivoMercantil($pagos): string
    {
        $emp         = self::EMPRESA_MERCANTIL;
        $total       = $pagos->sum('monto_total');
        $totalCents  = str_pad((int) round($total * 100), 15, '0', STR_PAD_LEFT);
        $count       = str_pad($pagos->count(), 7, '0', STR_PAD_LEFT);
        $cuenta      = $emp['cuenta'];

        // Header (linea 1) — longitud fija ~200 chars
        $header = '1'
            . str_pad('BAMRVECA', 12)
            . 'C1'
            . now()->format('Ymd')
            . now()->format('Hi')
            . str_pad($count, 7, '0', STR_PAD_LEFT)
            . 'DOMIC'
            . str_pad($emp['rif'], 11)
            . str_pad((int) round($total * 100), 12, '0', STR_PAD_LEFT)
            . '0'
            . str_pad('', 10, '0')
            . now()->format('Ymd')
            . str_pad($cuenta, 20)
            . $totalCents;
        $header = str_pad($header, 200);

        $lines = [$header];

        foreach ($pagos as $index => $pago) {
            $afil       = $pago->afilpagointegral;
            $letra      = $afil->letra ?? 'V';
            $cedula     = $afil->cedula_rif ?? '';
            $cuentaCli  = str_pad($afil->cta_bancaria ?? '', 20, '0', STR_PAD_RIGHT);
            $cedPad     = str_pad($cedula, 10, '0', STR_PAD_LEFT);
            $montoCents = str_pad((int) round((float) $pago->monto_total * 100), 17, '0', STR_PAD_LEFT);
            $numApto    = $afil->afilaptos->first()?->apartamento?->num_apto ?? '';
            $seq        = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $ref        = str_pad('000000000456' . $seq, 15, '0', STR_PAD_LEFT);

            $detalle = $pago->pagoIntegralDetalles->first();
            $periodo = '0000';
            if ($detalle && $detalle->periodo) {
                try {
                    $periodo = \Carbon\Carbon::parse($detalle->periodo . '-01')->format('Ym') . '01';
                } catch (\Exception $e) {
                    $periodo = now()->format('Ym') . '01';
                }
            }

            $line = '2'
                . $letra
                . $cedPad
                . $cuentaCli
                . str_pad('', 10)
                . str_pad($cedula, 17)
                . str_pad('', 17)
                . $montoCents
                . str_pad('', 30)
                . str_pad($numApto, 17)
                . str_pad('', 9)
                . $ref
                . '0'
                . $periodo
                . '0000'
                . str_pad('', 30)
                . $ref
                . '0'
                . str_pad('0', 15, '0')
                . str_pad('Pago Condominio', 35);

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Formato Banesco: HDR + 01 (batch) + 02 (empresa) + 03 (detalles) + 04 (trailer)
     * Basado en modelo real Bcobro.txt
     */
    private function generarArchivoBanesco($pagos): string
    {
        $emp        = self::EMPRESA_BANESCO;
        $total      = $pagos->sum('monto_total');
        $totalCents = str_pad((int) round($total * 100), 15, '0', STR_PAD_LEFT);
        $hoy        = now()->format('Ymd');
        $hora       = now()->format('His');

        // Linea HDR
        $hdr = 'HDR' . str_pad('BANESCO', 14) . 'ED  96ADIRDEBP';

        // Linea 01 — Batch header
        $linea01 = '01SUB'
            . str_pad('', 32)
            . '9'
            . str_pad('', 2)
            . str_pad($emp['servicio'], 3)
            . str_pad('', 36)
            . $hoy . $hora;

        // Linea 02 — Datos empresa
        $linea02 = '02'
            . str_pad('00000' . $emp['servicio'], 8)
            . str_pad('', 22)
            . str_pad($emp['rif'], 17)
            . str_pad($emp['nombre'], 40)
            . $totalCents
            . 'VES'
            . ' '
            . str_pad($emp['cuenta'], 20)
            . str_pad('', 15)
            . str_pad($emp['cod_banco'], 10)
            . str_pad('', 2)
            . $hoy
            . 'CB ';

        // Lineas 03 — Detalles
        $lines = [$hdr, $linea01, $linea02];
        $countDebits  = 0;
        $countCredits = $pagos->count();

        foreach ($pagos as $pago) {
            $afil    = $pago->afilpagointegral;
            $letra   = $afil->letra ?? 'V';
            $cedula  = $afil->cedula_rif ?? '';
            $nombre  = str_pad(trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? '')), 30);
            $cuenta  = str_pad($afil->cta_bancaria ?? '', 20);
            $monto   = str_pad((int) round((float) $pago->monto_total * 100), 15, '0', STR_PAD_LEFT);
            $cedulaId = $letra . $cedula;

            $linea03 = '03'
                . $hoy
                . str_pad('', 22)
                . $monto
                . 'VES'
                . $cuenta
                . str_pad('', 10)
                . str_pad($emp['cod_banco'], 10)
                . ' '
                . str_pad($cedulaId, 17)
                . str_pad('', 9)
                . $nombre
                . str_pad('', 11)
                . str_pad($cedulaId, 17)
                . str_pad('', 33);

            $lines[] = $linea03;
        }

        // Linea 04 — Trailer
        $linea04 = '04'
            . str_pad($countDebits + 1, 15, '0', STR_PAD_LEFT)
            . str_pad($countCredits, 15, '0', STR_PAD_LEFT)
            . $totalCents;

        $lines[] = $linea04;

        return implode("\n", $lines);
    }

    /**
     * Formato Bancaribe: sin header/trailer, cada linea es un registro slash-delimited
     * Formato: {letra}{cedula}/{cuenta}/{monto con formato}/{nombre}/{fechaYmd}/{ddmmyyyy}
     * Basado en modelo real BCcobro.txt
     */
    private function generarArchivoBancaribe($pagos): string
    {
        $lines = [];
        $hoy = now()->format('Ymd');

        foreach ($pagos as $pago) {
            $afil      = $pago->afilpagointegral;
            $letra     = $afil->letra ?? 'V';
            $cedula    = $afil->cedula_rif ?? '';
            $cuenta    = $afil->cta_bancaria ?? '';
            $monto     = number_format((float) $pago->monto_total, 2, '.', ',');
            $nombre    = strtoupper(trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? '')));

            // Fecha del periodo: DDMMYYYY
            $detalle = $pago->pagoIntegralDetalles->first();
            if ($detalle && $detalle->periodo) {
                try {
                    $periodoDate = \Carbon\Carbon::parse($detalle->periodo . '-01');
                    $periodoStr = $periodoDate->format('dm') . $periodoDate->format('Y');
                } catch (\Exception $e) {
                    $periodoStr = '01' . now()->format('mY');
                }
            } else {
                $periodoStr = '01' . $pago->fecha->format('mY');
            }

            $lines[] = "{$letra}{$cedula}/{$cuenta}/{$monto}/{$nombre}/{$hoy}/{$periodoStr}";
        }

        return implode("\n", $lines);
    }

    public function afiliaciones(Request $request)
    {
        $query = Afilpagointegral::with([
            'banco',
            'afilaptos.apartamento',
            'afilaptos.edificio',
        ]);

        if ($request->filled('cedula')) {
            $query->where('cedula_rif', 'like', '%' . $request->cedula . '%');
        }

        if ($request->filled('nombre')) {
            $nombre = $request->nombre;
            $query->where(function ($q) use ($nombre) {
                $q->where('nombres', 'ilike', "%{$nombre}%")
                  ->orWhere('apellidos', 'ilike', "%{$nombre}%");
            });
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('banco_id')) {
            $query->where('banco_id', $request->banco_id);
        }

        if ($request->filled('tipo_operacion')) {
            $query->where('tipo_operacion', $request->tipo_operacion);
        }

        if ($request->filled('mercantil_estatus')) {
            $query->where('mercantil_estatus_proceso', $request->mercantil_estatus);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Pestaña activa: afiliaciones | nuevas | desafiliaciones
        $tab = request('tab', 'afiliaciones');

        if ($tab === 'afiliaciones') {
            $query->where('tipo_operacion', 'A')->where('estatus', 'A');
        } elseif ($tab === 'nuevas') {
            $query->where('tipo_operacion', 'A')->where('estatus', 'P');
        } else {
            $query->where('tipo_operacion', 'D');
        }

        $afiliaciones = $query->orderBy('apellidos')->paginate(25)->withQueryString();

        $stats = [
            'total'        => Afilpagointegral::count(),
            'activos'      => Afilpagointegral::where('estatus', 'A')->where('tipo_operacion', 'A')->count(),
            'pendientes'   => Afilpagointegral::where('estatus', 'P')->where('tipo_operacion', 'A')->count(),
            'desafiliados' => Afilpagointegral::where('tipo_operacion', 'D')->count(),
        ];

        $bancos = $this->bancosAfiliacion();

        // Contar pendientes Mercantil para mostrar botones de acción
        $mercantilBanco = Banco::where('iniciales', 'BM')->first();
        // Afiliaciones listas para generar Mdomi: solo estatus='P' sin archivo enviado
        $mercantilPendientesAfil = $mercantilBanco
            ? Afilpagointegral::where('banco_id', $mercantilBanco->id)
                ->where('tipo_operacion', 'A')
                ->where('estatus', 'P')
                ->whereNull('mercantil_archivo_enviado')
                ->count()
            : 0;
        // Desafiliaciones listas para generar Mdesdomi: estatus P o I, sin archivo enviado
        $mercantilPendientesDesafil = $mercantilBanco
            ? Afilpagointegral::where('banco_id', $mercantilBanco->id)
                ->where('tipo_operacion', 'D')
                ->whereIn('estatus', ['P', 'I'])
                ->whereNull('mercantil_archivo_enviado')
                ->count()
            : 0;
        // Archivos ya enviados esperando respuesta del banco
        $mercantilEsperandoRespuesta = $mercantilBanco
            ? Afilpagointegral::where('banco_id', $mercantilBanco->id)
                ->where('mercantil_estatus_proceso', 'P')
                ->whereNotNull('mercantil_archivo_enviado')
                ->count()
            : 0;

        return view('financiero.afiliaciones', compact(
            'afiliaciones', 'stats', 'bancos',
            'mercantilPendientesAfil', 'mercantilPendientesDesafil', 'mercantilEsperandoRespuesta'
        ));
    }

    private function bancosAfiliacion()
    {
        return Banco::whereIn('id', [3, 5, 8])   // Mercantil (3), Bancaribe (5), Banesco (8)
            ->orderBy('nombre')->get();
    }

    private function validarPrefijoCuenta(Request $request): void
    {
        $prefijos = [8 => '0134', 3 => '0105', 5 => '0114'];
        $bancoId  = (int) $request->banco_id;

        if ($request->filled('cta_bancaria') && $request->filled('banco_id') && isset($prefijos[$bancoId])) {
            $prefijo = $prefijos[$bancoId];
            if (!str_starts_with($request->cta_bancaria, $prefijo)) {
                $banco = Banco::find($bancoId);
                abort(422, "La cuenta de {$banco->nombre} debe comenzar con {$prefijo}.");
            }
        }
    }

    private function apartamentosParaSelect()
    {
        return Apartamento::with('edificio')
            ->get()
            ->sortBy(fn($a) => ($a->edificio->nombre ?? '') . $a->num_apto);
    }

    public function afiliacionForm()
    {
        $bancos      = $this->bancosAfiliacion();
        $estados     = Estado::orderBy('nombre')->get();
        $apartamentos = $this->apartamentosParaSelect();
        $afiliacion  = null;

        return view('financiero.afiliacion-form', compact('afiliacion', 'bancos', 'estados', 'apartamentos'));
    }

    public function storeAfiliacion(Request $request)
    {
        $prefijos = [8 => '0134', 3 => '0105', 5 => '0114'];

        $request->validate([
            'apartamento_id' => 'required|exists:cond_aptos,id',
            'letra'          => 'required|in:V,E,J,G,P',
            'cedula_rif'     => 'required|string|max:20',
            'nombres'        => 'required|string|max:100',
            'apellidos'      => 'required|string|max:100',
            'email'          => 'nullable|email|max:100',
            'email_alterno'  => 'nullable|email|max:100',
            'banco_id'       => 'nullable|exists:bancos,id',
            'cta_bancaria'   => 'nullable|string|max:20',
            'tipo_cta'       => 'nullable|string|max:20',
            'cod_sucursal'   => 'nullable|string|max:20',
            'telefono'       => 'nullable|string|max:20',
            'celular'        => 'nullable|string|max:20',
            'fax'            => 'nullable|string|max:20',
            'otro'           => 'nullable|string|max:20',
            'calle_avenida'  => 'nullable|string|max:200',
            'edif_casa'      => 'nullable|string|max:100',
            'piso_apto'      => 'nullable|string|max:50',
            'urbanizacion'   => 'nullable|string|max:100',
            'ciudad'         => 'nullable|string|max:100',
            'estado_id'      => 'nullable|exists:estados,id',
            'nom_usuario'    => 'nullable|string|max:100',
            'estatus'        => 'required|in:A,I,P',
            'observaciones'  => 'nullable|string',
            'cta_bancaria'   => [
                'nullable', 'string', 'size:20',
                function ($attr, $value, $fail) use ($request, $prefijos) {
                    $id = (int) $request->banco_id;
                    if ($value && isset($prefijos[$id]) && !str_starts_with($value, $prefijos[$id])) {
                        $fail("La cuenta debe comenzar con {$prefijos[$id]}.");
                    }
                },
            ],
        ]);

        $apartamento = Apartamento::findOrFail($request->apartamento_id);
        $compania    = Compania::first();

        $mercantilBanco = Banco::where('iniciales', 'BM')->first();
        $esMercantil = $mercantilBanco && (int) $request->banco_id === $mercantilBanco->id;

        $afiliacion = Afilpagointegral::create(array_merge(
            $request->except(['apartamento_id', '_token', '_method']),
            [
                'fecha'          => now()->toDateString(),
                'creado_por'     => auth()->user()->name,
                'tipo_operacion' => 'A',
            ]
        ));

        // Crear el afilapto vinculado a esta afiliación
        Afilapto::create([
            'afilpagointegral_id' => $afiliacion->id,
            'apartamento_id'      => $apartamento->id,
            'edificio_id'         => $apartamento->edificio_id,
            'compania_id'         => $compania?->id,
            'estatus_afil'        => 'A',
            'fecha_afiliacion'    => now()->toDateString(),
        ]);

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliacion registrada correctamente.' . ($esMercantil ? ' Recuerde generar el archivo Mdomi para enviar al Banco Mercantil.' : ''));
    }

    public function editAfiliacion(Afilpagointegral $afiliacion)
    {
        $afiliacion->load('afilaptos.apartamento.edificio');
        $bancos       = $this->bancosAfiliacion();
        $estados      = Estado::orderBy('nombre')->get();
        $apartamentos = $this->apartamentosParaSelect();

        return view('financiero.afiliacion-form', compact('afiliacion', 'bancos', 'estados', 'apartamentos'));
    }

    public function updateAfiliacion(Request $request, Afilpagointegral $afiliacion)
    {
        $prefijos = [8 => '0134', 3 => '0105', 5 => '0114'];

        $request->validate([
            'apartamento_id'             => 'nullable|exists:cond_aptos,id',
            'letra'                      => 'required|in:V,E,J,G,P',
            'cedula_rif'                 => 'required|string|max:20',
            'nombres'                    => 'required|string|max:100',
            'apellidos'                  => 'required|string|max:100',
            'email'                      => 'nullable|email|max:100',
            'email_alterno'              => 'nullable|email|max:100',
            'banco_id'                   => 'nullable|exists:bancos,id',
            'cta_bancaria'               => [
                'nullable', 'string', 'size:20',
                function ($attr, $value, $fail) use ($request, $prefijos) {
                    $id = (int) $request->banco_id;
                    if ($value && isset($prefijos[$id]) && !str_starts_with($value, $prefijos[$id])) {
                        $fail("La cuenta debe comenzar con {$prefijos[$id]}.");
                    }
                },
            ],
            'tipo_cta'                   => 'nullable|string|max:20',
            'cod_sucursal'               => 'nullable|string|max:20',
            'telefono'                   => 'nullable|string|max:20',
            'celular'                    => 'nullable|string|max:20',
            'fax'                        => 'nullable|string|max:20',
            'otro'                       => 'nullable|string|max:20',
            'calle_avenida'              => 'nullable|string|max:200',
            'edif_casa'                  => 'nullable|string|max:100',
            'piso_apto'                  => 'nullable|string|max:50',
            'urbanizacion'               => 'nullable|string|max:100',
            'ciudad'                     => 'nullable|string|max:100',
            'estado_id'                  => 'nullable|exists:estados,id',
            'nom_usuario'                => 'nullable|string|max:100',
            'estatus'                    => 'required|in:A,I,P',
            'observaciones'              => 'nullable|string',
            'tipo_operacion'             => 'nullable|in:A,D',
            'mercantil_estatus_proceso'  => 'nullable|in:P,A,R',
            'mercantil_archivo_enviado'  => 'nullable|string|max:100',
            'mercantil_fecha_envio'      => 'nullable|date',
            'mercantil_fecha_respuesta'  => 'nullable|date',
            'mercantil_cod_respuesta'    => 'nullable|string|max:10',
            'mercantil_mensaje'          => 'nullable|string|max:200',
        ]);

        // Si se seleccionó un nuevo inmueble, agregar un afilapto adicional a esta afiliación
        if ($request->filled('apartamento_id')) {
            $apartamento = Apartamento::findOrFail($request->apartamento_id);
            $compania    = Compania::first();
            // Solo agregar si este apartamento no está ya vinculado a esta afiliación
            $yaVinculado = Afilapto::where('afilpagointegral_id', $afiliacion->id)
                ->where('apartamento_id', $apartamento->id)
                ->exists();
            if (!$yaVinculado) {
                Afilapto::create([
                    'afilpagointegral_id' => $afiliacion->id,
                    'apartamento_id'      => $apartamento->id,
                    'edificio_id'         => $apartamento->edificio_id,
                    'compania_id'         => $compania?->id,
                    'estatus_afil'        => 'A',
                    'fecha_afiliacion'    => now()->toDateString(),
                ]);
            }
        }

        $datos = $request->except(['apartamento_id', '_token', '_method']);

        // Si se cambia tipo_operacion a 'D' en banco directo (no Mercantil), forzar estatus 'I'
        $afiliacion->refresh(); // asegura banco cargado
        $bancoId = $request->filled('banco_id') ? (int) $request->banco_id : $afiliacion->banco_id;
        $bancoMerc = Banco::where('iniciales', 'BM')->first();
        $esMercantil = $bancoMerc && $bancoId === $bancoMerc->id;

        if (($datos['tipo_operacion'] ?? $afiliacion->tipo_operacion) === 'D' && !$esMercantil) {
            $datos['estatus'] = 'I';
        }

        $afiliacion->update($datos);

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliacion actualizada correctamente.');
    }

    public function desafiliar(Afilpagointegral $afiliacion)
    {
        $data = [
            'estatus'        => 'I',
            'tipo_operacion' => 'D',
            'observaciones'  => ($afiliacion->observaciones ? $afiliacion->observaciones . ' | ' : '')
                                . 'Desafiliado por ' . auth()->user()->name . ' el ' . now()->format('d/m/Y H:i'),
        ];

        // Si es Mercantil, marcar como pendiente de envío de archivo
        if ($afiliacion->esMercantil()) {
            $data['mercantil_estatus_proceso'] = 'P';
        }

        $afiliacion->update($data);

        Afilapto::where('afilpagointegral_id', $afiliacion->id)->update(['estatus_afil' => 'D']);

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliado desafiliado correctamente.' . ($afiliacion->esMercantil() ? ' Recuerde generar el archivo Mdesdomi para Mercantil.' : ''));
    }

    public function reactivar(Afilpagointegral $afiliacion)
    {
        $afiliacion->update([
            'estatus'                   => 'A',
            'tipo_operacion'            => 'A',
            'mercantil_estatus_proceso' => null,
            'observaciones'             => ($afiliacion->observaciones ? $afiliacion->observaciones . ' | ' : '')
                                           . 'Reactivado por ' . auth()->user()->name . ' el ' . now()->format('d/m/Y H:i'),
        ]);

        Afilapto::where('afilpagointegral_id', $afiliacion->id)->update(['estatus_afil' => 'A']);

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliacion reactivada correctamente.');
    }

    // ══════════════════════════════════════════════════════════════
    // PROCESO MERCANTIL: GENERACIÓN Y PROCESAMIENTO DE ARCHIVOS
    // ══════════════════════════════════════════════════════════════

    /**
     * Genera el archivo Mdomi (afiliaciones) o Mdesdomi (desafiliaciones) para Mercantil.
     * Formato posicional fijo según especificación bancaria.
     * Línea 1: Header  — comienza con "1"
     * Línea 2+: Detalle — comienza con "2A" (afiliación) o "2D" (desafiliación)
     */
    public function generarArchivoMercantilAfiliacion(Request $request)
    {
        $request->validate([
            'tipo_operacion' => 'required|in:A,D',
        ]);

        $tipo       = $request->tipo_operacion; // A o D
        $esDesa     = $tipo === 'D';
        $bancoMerc  = Banco::where('iniciales', 'BM')->first();

        if (!$bancoMerc) {
            return back()->with('error', 'Banco Mercantil no encontrado en el sistema.');
        }

        // Traer afiliaciones Mercantil pendientes del tipo indicado
        $query = Afilpagointegral::with(['afilaptos.apartamento', 'afilaptos.edificio', 'banco'])
            ->where('banco_id', $bancoMerc->id)
            ->where('tipo_operacion', $tipo);

        if ($esDesa) {
            // Desafiliaciones: estatus='P' o 'I', sin archivo enviado aún
            $query->whereIn('estatus', ['P', 'I'])
                  ->whereNull('mercantil_archivo_enviado');
        } else {
            // Afiliaciones: solo estatus='P' (pendiente de aprobación bancaria) sin archivo enviado
            $query->where('estatus', 'P')
                  ->whereNull('mercantil_archivo_enviado');
        }

        $afiliaciones = $query->get();

        if ($afiliaciones->isEmpty()) {
            return back()->with('error', 'No hay ' . ($esDesa ? 'desafiliaciones' : 'afiliaciones') . ' Mercantil pendientes de envío.');
        }

        $emp    = self::EMPRESA_MERCANTIL;
        $hoy    = now()->format('Ymd');
        $hora   = now()->format('Hi');
        $count  = str_pad($afiliaciones->count(), 7, '0', STR_PAD_LEFT);

        // ── Header ──
        // Basado en Mdomi267.txt: 1BAMRVECA    202623021102112J0001426434DOMIC...
        $header = '1'
            . str_pad('BAMRVECA', 12)
            . $hoy
            . $hora
            . '1'                                     // secuencia lote
            . $count
            . 'J' . str_pad(ltrim($emp['rif'], 'J'), 10)
            . 'DOMIC'
            . now()->format('Ymd')
            . str_pad('1', 10, '0', STR_PAD_LEFT)
            . str_pad($emp['cuenta'], 20);
        $header = str_pad($header, 200);

        $lines = [$header];

        foreach ($afiliaciones as $afil) {
            $tipoLinea  = $esDesa ? 'D' : 'A';        // 2A o 2D
            $letra      = $afil->letra ?? 'V';
            $cedula     = str_pad($afil->cedula_rif ?? '', 9, '0', STR_PAD_LEFT);
            $cuenta     = str_pad($afil->cta_bancaria ?? '', 20, '0', STR_PAD_RIGHT);
            $nombre     = strtoupper(trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? '')));
            $cedFull    = $letra . str_pad($afil->cedula_rif ?? '', 10, '0', STR_PAD_LEFT);
            $telefono   = str_pad(preg_replace('/\D/', '', $afil->celular ?? $afil->telefono ?? ''), 11, '0', STR_PAD_LEFT);
            $email      = str_pad($afil->email ?? '', 40);
            $numApto    = $afil->afilaptos->first()?->apartamento?->num_apto ?? '';
            $codSuc     = str_pad($afil->cod_sucursal ?? '0000', 4, '0', STR_PAD_LEFT);

            // Formato basado en Mdomi267.txt línea "2A":
            // 2A{cedula9}{cuenta20}{espacios10}{cedula17}{espacios17}{telefono11}{fecha8}{email40}{nombre30}...
            $detalle = '2' . $tipoLinea
                . $cedula
                . $cuenta
                . str_pad('', 10)
                . str_pad($cedFull, 17)
                . str_pad('', 17)
                . $telefono
                . $hoy
                . $email
                . str_pad($nombre, 30)
                . str_pad($numApto, 17)
                . $codSuc
                . str_pad('', 30);

            $lines[] = str_pad($detalle, 200);
        }

        $content  = implode("\r\n", $lines);
        $prefix   = $esDesa ? 'Mdesdomi' : 'Mdomi';
        $filename = $prefix . $afiliaciones->count() . '.txt';

        // Marcar afiliaciones como archivo enviado y pendiente de respuesta
        $afiliaciones->each(fn($a) => $a->update([
            'mercantil_archivo_enviado'  => $filename,
            'mercantil_fecha_envio'      => now()->toDateString(),
            'mercantil_estatus_proceso'  => 'P',
        ]));

        return response($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Anula (revierte) un archivo Mdomi/Mdesdomi ya generado,
     * limpiando los campos mercantil para que los registros vuelvan a estar disponibles.
     */
    public function anularArchivoMercantilAfiliacion(Request $request)
    {
        $request->validate([
            'archivo'        => 'required|string|max:100',
            'tipo_operacion' => 'required|in:A,D',
        ]);

        $archivo = $request->archivo;
        $tipo    = $request->tipo_operacion;

        $registros = Afilpagointegral::where('mercantil_archivo_enviado', $archivo)
            ->where('tipo_operacion', $tipo)
            ->where('mercantil_estatus_proceso', 'P') // solo los que aún no tienen respuesta
            ->get();

        if ($registros->isEmpty()) {
            return back()->with('error', "No se encontraron registros anulables para el archivo «{$archivo}». Es posible que ya tengan respuesta procesada.");
        }

        $registros->each(fn($a) => $a->update([
            'mercantil_archivo_enviado'  => null,
            'mercantil_fecha_envio'      => null,
            'mercantil_estatus_proceso'  => null,
        ]));

        return back()->with('success', "Archivo «{$archivo}» anulado. {$registros->count()} registro(s) volvieron a estar disponibles para generar un nuevo archivo.");
    }

    /**
     * Formulario para procesar la respuesta de Mercantil (afiliaciones/desafiliaciones).
     */
    public function procesarRespuestaMercantilAfiliacionForm()
    {
        $pendientes = Afilpagointegral::where('mercantil_estatus_proceso', 'P')
            ->whereNotNull('mercantil_archivo_enviado')
            ->with('banco')
            ->get()
            ->groupBy('tipo_operacion');

        return view('financiero.pago-integral-respuesta-afiliacion-mercantil', compact('pendientes'));
    }

    /**
     * Procesa el archivo de respuesta de Mercantil para afiliaciones/desafiliaciones.
     * Formato línea "2D" o "2A": tipo+letra+cédula(9)+...+código_respuesta(4)+mensaje
     */
    public function procesarRespuestaMercantilAfiliacion(Request $request)
    {
        $request->validate([
            'archivo_respuesta' => 'required|file|mimes:txt,dat|max:5120',
            'tipo_operacion'    => 'required|in:A,D',
        ]);

        $tipo    = $request->tipo_operacion;
        $content = file_get_contents($request->file('archivo_respuesta')->getRealPath());
        $lines   = explode("\n", str_replace("\r\n", "\n", $content));

        $resultados   = [];
        $aprobados    = 0;
        $rechazados   = 0;
        $noEncontrados = 0;

        foreach ($lines as $line) {
            $line = rtrim($line);
            if (strlen($line) < 12) continue;

            $tipoLinea = substr($line, 0, 2); // "2A" o "2D"
            if ($tipoLinea !== '2' . $tipo && $tipoLinea[0] !== '2') continue;
            if ($line[0] !== '2') continue;

            // Cédula: posición 2-10 (9 chars, padded con ceros)
            $cedulaPad = substr($line, 2, 9);
            $cedula    = ltrim($cedulaPad, '0');

            // Código respuesta Mercantil — posición variable, buscar en últimos 50 chars
            // Formato Mdesdomi respuesta: código en pos ~100-104
            $codResp = trim(substr($line, 100, 4));
            $mensaje = trim(substr($line, 104, 40));
            $exitoso = $codResp === '0074' || $codResp === '0000';

            // Buscar afiliación por cédula
            $afil = Afilpagointegral::where('cedula_rif', $cedula)
                ->where('tipo_operacion', $tipo)
                ->where('mercantil_estatus_proceso', 'P')
                ->first();

            if (!$afil) {
                $noEncontrados++;
                $resultados[] = ['cedula' => $cedula, 'estado' => 'no_encontrado', 'mensaje' => 'No encontrado en sistema', 'cod' => $codResp];
                continue;
            }

            $afil->update([
                'mercantil_estatus_proceso'  => $exitoso ? 'A' : 'R',
                'mercantil_fecha_respuesta'  => now()->toDateString(),
                'mercantil_cod_respuesta'    => $codResp,
                'mercantil_mensaje'          => $mensaje ?: ($exitoso ? 'APROBADO' : 'RECHAZADO'),
            ]);

            if ($exitoso) {
                $aprobados++;
                $resultados[] = ['cedula' => $cedula, 'estado' => 'aprobado', 'mensaje' => $mensaje ?: 'APROBADO', 'cod' => $codResp];
            } else {
                $rechazados++;
                $resultados[] = ['cedula' => $cedula, 'estado' => 'rechazado', 'mensaje' => $mensaje ?: 'RECHAZADO', 'cod' => $codResp];
            }
        }

        return view('financiero.pago-integral-respuesta-afiliacion-mercantil', compact('resultados', 'aprobados', 'rechazados', 'noEncontrados'));
    }
}
