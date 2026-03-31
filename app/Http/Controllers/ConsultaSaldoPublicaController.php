<?php

namespace App\Http\Controllers;

use App\Models\Catalogo\TasaBcv;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Propietario;
use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPagoApto;
use Illuminate\Http\Request;

class ConsultaSaldoPublicaController extends Controller
{
    public function consultar(Request $request)
    {
        $request->validate([
            'tipo_busqueda' => 'required|in:pint,cedula',
            'valor' => 'required|string|max:50',
        ]);

        $tipo = $request->tipo_busqueda;
        $valor = trim($request->valor);

        $apartamentos = collect();

        if ($tipo === 'pint') {
            $afilapto = Afilapto::where('cod_pint', $valor)
                ->where('estatus_afil', 'A')
                ->first();

            if ($afilapto) {
                $apto = Apartamento::with('edificio')->find($afilapto->apartamento_id);
                if ($apto) {
                    $apartamentos = collect([$apto]);
                }
            }
        } else {
            $propietario = Propietario::where('cedula', $valor)->first();

            if ($propietario) {
                $apartamentos = $propietario->apartamentos()
                    ->wherePivot('propietario_actual', true)
                    ->with('edificio')
                    ->get();
            }
        }

        if ($apartamentos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $tipo === 'pint'
                    ? 'No se encontro un apartamento con el codigo PINT ingresado.'
                    : 'No se encontro un propietario con la cedula ingresada.',
            ]);
        }

        // Tasa BCV de hoy (la mas reciente)
        $tasaBcvHoy = TasaBcv::where('moneda', 'USD')
            ->orderByDesc('fecha')
            ->first();

        // Pre-cargar tasas del 1ro de cada mes para calcular REF
        $tasasPrimerDia = TasaBcv::where('moneda', 'USD')
            ->whereRaw("EXTRACT(DAY FROM fecha) = 1")
            ->pluck('tasa', 'fecha')
            ->mapWithKeys(fn($tasa, $fecha) => [substr($fecha, 0, 7) => $tasa])
            ->toArray();

        $resultados = [];

        foreach ($apartamentos as $apto) {
            $deudas = CondDeudaApto::where('apartamento_id', $apto->id)
                ->orderBy('periodo')
                ->get();

            $deudasPendientes = $deudas->filter(function ($d) {
                return (is_null($d->fecha_pag) || $d->fecha_pag == '0001-01-01')
                    && (is_null($d->serial) || $d->serial === 'N');
            });

            $cantidadPendientes = $deudasPendientes->count();
            $extrajudicial = $cantidadPendientes > 4;

            $edificioNombre = $apto->edificio
                ? $apto->edificio->cod_edif . ' - ' . $apto->edificio->nombre
                : 'N/A';

            // Si es extrajudicial: calcular deuda total (pendientes + mora)
            if ($extrajudicial) {
                $deudaTotal = $deudasPendientes->sum(function ($d) {
                    return $d->monto_original + ($d->monto_mora ?? 0) + ($d->monto_interes ?? 0) - ($d->monto_descuento ?? 0);
                });

                $resultados[] = [
                    'edificio' => $edificioNombre,
                    'unidad' => $apto->num_apto,
                    'extrajudicial' => true,
                    'cantidad_pendientes' => $cantidadPendientes,
                    'deuda_total' => number_format($deudaTotal, 2, ',', '.'),
                    'deudas' => [],
                ];
            } else {
                // Solo mostrar los ultimos 4 meses de deudas
                $ultimasDeudas = $deudas->sortByDesc('periodo')->take(4)->sortBy('periodo');

                $resultados[] = [
                    'edificio' => $edificioNombre,
                    'unidad' => $apto->num_apto,
                    'extrajudicial' => false,
                    'cantidad_pendientes' => $cantidadPendientes,
                    'deuda_total' => '0,00',
                    'deudas' => $ultimasDeudas->map(function ($d) use ($tasaBcvHoy, $tasasPrimerDia) {
                        $pagado = !is_null($d->fecha_pag) && $d->fecha_pag != '0001-01-01'
                            && !is_null($d->serial) && $d->serial !== 'N';

                        // Tasa del 1ro del mes del periodo para calcular REF
                        $periodoKey = $d->periodo; // formato YYYY-MM
                        $tasaPrimerDia = $tasasPrimerDia[$periodoKey] ?? null;

                        // Si no hay tasa del 1ro exacto, buscar la mas cercana al inicio del mes
                        if (!$tasaPrimerDia) {
                            $inicioMes = $periodoKey . '-01';
                            $tasaCercana = TasaBcv::where('moneda', 'USD')
                                ->whereDate('fecha', '>=', $inicioMes)
                                ->whereDate('fecha', '<=', $periodoKey . '-05')
                                ->orderBy('fecha')
                                ->first();
                            if (!$tasaCercana) {
                                $tasaCercana = TasaBcv::where('moneda', 'USD')
                                    ->whereDate('fecha', '<=', $inicioMes)
                                    ->orderByDesc('fecha')
                                    ->first();
                            }
                            $tasaPrimerDia = $tasaCercana ? $tasaCercana->tasa : 0;
                        }

                        // REF = monto / tasa del 1ro del mes del periodo
                        $montoRef = $tasaPrimerDia > 0 ? round($d->monto_original / $tasaPrimerDia, 2) : 0;

                        // Total: pagado = REF x tasa del dia de pago | pendiente = REF x tasa de hoy
                        $abono = $d->monto_pagado ?? 0;

                        if ($pagado && $d->fecha_pag) {
                            // Buscar tasa BCV de la fecha de pago
                            $tasaPago = TasaBcv::where('moneda', 'USD')
                                ->whereDate('fecha', '<=', $d->fecha_pag)
                                ->orderByDesc('fecha')
                                ->first();
                            $tasaTotal = $tasaPago ? $tasaPago->tasa : ($tasaBcvHoy ? $tasaBcvHoy->tasa : 0);
                        } else {
                            $tasaTotal = $tasaBcvHoy ? $tasaBcvHoy->tasa : 0;
                        }

                        $total = round($montoRef * $tasaTotal, 2);

                        return [
                            'periodo' => $d->periodo,
                            'monto' => number_format($d->monto_original, 2, ',', '.'),
                            'monto_ref' => number_format($montoRef, 2, ',', '.'),
                            'tasa_usada' => number_format($tasaPrimerDia, 2, ',', '.'),
                            'abono' => number_format($abono, 2, ',', '.'),
                            'total' => number_format($total, 2, ',', '.'),
                            'fecha_pago' => $pagado && $d->fecha_pag ? $d->fecha_pag->format('d/m/Y') : null,
                            'estatus' => $pagado ? 'PAGADO' : 'PENDIENTE',
                            'comprobante' => $d->serial && $d->serial !== 'N' ? $d->serial : null,
                        ];
                    })->values(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'tasa_bcv' => $tasaBcvHoy ? number_format($tasaBcvHoy->tasa, 2, ',', '.') : 'N/A',
            'resultados' => $resultados,
        ]);
    }
}
