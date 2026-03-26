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
use App\Models\Financiero\PagoIntegralDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoIntegralController extends Controller
{
    public function index()
    {
        $pendientes = PagoIntegral::where('estatus', 'P')
            ->with(['afilpagointegral.afilapto.apartamento.edificio', 'compania', 'pagoIntegralDetalles'])
            ->latest('fecha')->get();

        $procesados = PagoIntegral::whereIn('estatus', ['A', 'R'])
            ->with(['afilpagointegral.afilapto.apartamento.edificio', 'compania', 'pagoIntegralDetalles'])
            ->latest('fecha')->paginate(15);

        $totalPendiente = PagoIntegral::where('estatus', 'P')->sum('monto_total');
        $countPendiente = PagoIntegral::where('estatus', 'P')->count();
        $countAprobados = PagoIntegral::where('estatus', 'A')->count();
        $countRechazados = PagoIntegral::where('estatus', 'R')->count();

        return view('financiero.pago-integral', compact(
            'pendientes', 'procesados', 'totalPendiente', 'countPendiente', 'countAprobados', 'countRechazados'
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
            if ($pago->afilpagointegral && $pago->afilpagointegral->afilapto) {
                $apartamentoId = $pago->afilpagointegral->afilapto->apartamento_id;
                $periodos = $pago->pagoIntegralDetalles->pluck('periodo')->toArray();

                CondDeudaApto::where('apartamento_id', $apartamentoId)
                    ->whereIn('periodo', $periodos)
                    ->update([
                        'estatus' => 'C',
                        'monto_pagado' => DB::raw('monto_original'),
                        'saldo' => 0,
                        'fecha_pago' => $pago->fecha,
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
            'afilpagointegral.afilapto.apartamento',
            'afilpagointegral.afilapto.edificio',
            'afilpagointegral.afilapto.compania',
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
                'afilpagointegral.afilapto.apartamento',
                'afilpagointegral.afilapto.edificio',
                'afilpagointegral.afilapto.compania',
                'pagoIntegralDetalles',
            ])
            ->get();

        $content  = $this->generarContenidoArchivo($pagos, $banco, $request->tipo_archivo);
        $filename = $banco->iniciales . 'cobro' . $pagos->count() . '.txt';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/plain']);
    }

    private function generarContenidoArchivo($pagos, $banco, $tipo): string
    {
        $iniciales = strtoupper(trim($banco->iniciales ?? ''));

        if (in_array($iniciales, ['BC', 'BANCARIBE'])) {
            $lines = [];
            foreach ($pagos as $pago) {
                $afil      = $pago->afilpagointegral;
                $letra     = $afil->letra ?? 'V';
                $cedula    = $afil->cedula_rif;
                $cuenta    = $afil->cta_bancaria;
                $monto     = str_replace(',', '', number_format((float) $pago->monto_total, 2));
                $nombre    = trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? ''));
                $hoy       = now()->format('Ymd');
                $detalle   = $pago->pagoIntegralDetalles->first();
                if ($detalle && isset($detalle->periodo)) {
                    $periodoStr = \Carbon\Carbon::parse('01-' . $detalle->periodo)->format('dmY');
                } else {
                    $periodoStr = '01' . $pago->fecha->format('mY');
                }
                $lines[] = "{$letra}{$cedula}/{$cuenta}/{$monto}/{$nombre}/{$hoy}/{$periodoStr}";
            }
            return implode("\n", $lines);
        }

        if (in_array($iniciales, ['BM', 'MERCANTIL', 'BAMR'])) {
            $total       = $pagos->sum('monto_total');
            $totalCents  = str_pad((int) round($total * 100), 12, '0', STR_PAD_LEFT);
            $count       = str_pad($pagos->count(), 7, '0', STR_PAD_LEFT);
            $rifCompania = str_pad('J0001426434', 11);
            $header      = str_pad(
                '1' . str_pad('BAMRVECA', 12) . 'C' . '1' . now()->format('Ymd') . now()->format('Hi') . '00' . $count . 'DOMIC' . $rifCompania . $totalCents . '0',
                200
            );

            $lines = [$header];
            foreach ($pagos as $index => $pago) {
                $afil      = $pago->afilpagointegral;
                $letra     = $afil->letra ?? 'V';
                $cedula    = $afil->cedula_rif;
                $cuenta    = str_pad($afil->cta_bancaria ?? '', 20);
                $cedPad    = str_pad($cedula, 10, '0', STR_PAD_LEFT);
                $montoCents = str_pad((int) round((float) $pago->monto_total * 100), 17, '0', STR_PAD_LEFT);
                $numApto   = $afil->afilapto->apartamento->num_apto ?? '';
                $ref       = str_pad('000000000456' . str_pad($index + 1, 3, '0', STR_PAD_LEFT), 16);
                $line = '2'
                    . $letra
                    . $cedPad
                    . $cuenta
                    . str_pad('', 10)
                    . str_pad($cedula, 17)
                    . str_pad('', 17)
                    . $montoCents
                    . str_pad('', 30)
                    . str_pad($numApto, 17)
                    . str_pad('', 9)
                    . $ref
                    . '0'
                    . now()->format('Ymd')
                    . '0000'
                    . str_pad('', 30)
                    . $ref
                    . '0'
                    . str_pad('0', 15)
                    . str_pad('Pago Condominio', 35);
                $lines[] = $line;
            }
            return implode("\n", $lines);
        }

        if (in_array($iniciales, ['BB', 'BANESCO'])) {
            $codEmpresa = str_pad('366', 36);
            $rif        = str_pad('J001426434', 17);
            $header = '01'
                . $codEmpresa
                . now()->format('YmdHis')
                . 'SUB'
                . $codEmpresa
                . str_pad($banco->nombre ?? '', 60)
                . $rif
                . str_pad('ADMINISTRADORA INTEGRAL', 60);

            $lines = [$header];
            foreach ($pagos as $pago) {
                $afil       = $pago->afilpagointegral;
                $letra      = $afil->letra ?? 'V';
                $cedula     = $afil->cedula_rif;
                $montoCents = str_pad((int) round((float) $pago->monto_total * 100), 18, '0', STR_PAD_LEFT);
                $nombre     = trim(($afil->nombres ?? '') . ' ' . ($afil->apellidos ?? ''));
                $cuenta     = str_pad($afil->cta_bancaria ?? '', 20);
                $line = '022'
                    . now()->format('Ymd')
                    . str_pad('', 27)
                    . str_pad($letra . $cedula, 35)
                    . str_pad('', 26)
                    . now()->format('Ymd')
                    . $montoCents
                    . 'VES'
                    . str_pad($letra . $cedula, 12)
                    . str_pad('', 7)
                    . $cuenta
                    . str_pad('', 15)
                    . str_pad('BANSVECA', 60)
                    . str_pad('', 44)
                    . str_pad($nombre, 60)
                    . str_pad('', 44);
                $lines[] = $line;
            }
            return implode("\n", $lines);
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

    public function afiliaciones(Request $request)
    {
        $query = Afilpagointegral::with([
            'banco',
            'afilapto.apartamento',
            'afilapto.edificio',
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

        $afiliaciones = $query->orderBy('apellidos')->paginate(25)->withQueryString();

        $stats = [
            'total'      => Afilpagointegral::count(),
            'activos'    => Afilpagointegral::where('estatus', 'A')->count(),
            'pendientes' => Afilpagointegral::where('estatus', 'P')->count(),
            'inactivos'  => Afilpagointegral::where('estatus', 'I')->count(),
        ];

        return view('financiero.afiliaciones', compact('afiliaciones', 'stats'));
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

        $afilapto = Afilapto::firstOrCreate(
            ['apartamento_id' => $apartamento->id],
            [
                'edificio_id'       => $apartamento->edificio_id,
                'compania_id'       => $compania?->id,
                'estatus_afil'      => 'A',
                'fecha_afiliacion'  => now()->toDateString(),
            ]
        );

        Afilpagointegral::create(array_merge(
            $request->except(['apartamento_id', '_token', '_method']),
            [
                'afilapto_id' => $afilapto->id,
                'fecha'       => now()->toDateString(),
                'creado_por'  => auth()->user()->name,
            ]
        ));

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliacion registrada correctamente.');
    }

    public function editAfiliacion(Afilpagointegral $afiliacion)
    {
        $bancos       = $this->bancosAfiliacion();
        $estados      = Estado::orderBy('nombre')->get();
        $apartamentos = $this->apartamentosParaSelect();

        return view('financiero.afiliacion-form', compact('afiliacion', 'bancos', 'estados', 'apartamentos'));
    }

    public function updateAfiliacion(Request $request, Afilpagointegral $afiliacion)
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
            'cta_bancaria'   => [
                'nullable', 'string', 'size:20',
                function ($attr, $value, $fail) use ($request, $prefijos) {
                    $id = (int) $request->banco_id;
                    if ($value && isset($prefijos[$id]) && !str_starts_with($value, $prefijos[$id])) {
                        $fail("La cuenta debe comenzar con {$prefijos[$id]}.");
                    }
                },
            ],
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
        ]);

        $apartamento = Apartamento::findOrFail($request->apartamento_id);
        $compania    = Compania::first();

        $afilapto = Afilapto::firstOrCreate(
            ['apartamento_id' => $apartamento->id],
            [
                'edificio_id'      => $apartamento->edificio_id,
                'compania_id'      => $compania?->id,
                'estatus_afil'     => 'A',
                'fecha_afiliacion' => now()->toDateString(),
            ]
        );

        $afiliacion->update(array_merge(
            $request->except(['apartamento_id', '_token', '_method']),
            ['afilapto_id' => $afilapto->id]
        ));

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliacion actualizada correctamente.');
    }

    public function desafiliar(Afilpagointegral $afiliacion)
    {
        $afiliacion->update([
            'estatus'       => 'I',
            'observaciones' => ($afiliacion->observaciones ? $afiliacion->observaciones . ' | ' : '')
                               . 'Desafiliado por ' . auth()->user()->name . ' el ' . now()->format('d/m/Y H:i'),
        ]);

        return redirect()->route('financiero.pago-integral.afiliaciones')
            ->with('success', 'Afiliado desafiliado correctamente.');
    }
}
