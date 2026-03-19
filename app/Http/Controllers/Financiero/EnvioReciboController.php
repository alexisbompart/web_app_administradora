<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Mail\ReciboCondominio;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondDeudaApto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnvioReciboController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cobranza.ver');
    }

    public function index(Request $request)
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $apartamentos = collect();
        $deudas = collect();
        $edificio = null;
        $periodo = $request->input('periodo', now()->format('Y-m'));

        if ($request->filled('edificio_id')) {
            $edificio = Edificio::find($request->edificio_id);

            $apartamentos = Apartamento::where('edificio_id', $request->edificio_id)
                ->orderBy('num_apto')
                ->get();

            $query = CondDeudaApto::where('edificio_id', $request->edificio_id)
                ->where('estatus', 'P')
                ->with(['apartamento']);

            if ($request->filled('periodo')) {
                $query->where('periodo', $request->periodo);
            }

            $deudas = $query->orderBy('apartamento_id')->get()->map(function ($deuda) {
                $apto = $deuda->apartamento;
                return (object) [
                    'id' => $deuda->id,
                    'apartamento_id' => $deuda->apartamento_id,
                    'num_apto' => $apto->num_apto ?? 'N/A',
                    'propietario_nombre' => $apto->propietario_nombre ?? 'N/A',
                    'propietario_email' => $apto->propietario_email,
                    'periodo' => $deuda->periodo,
                    'monto_original' => $deuda->monto_original,
                    'saldo' => $deuda->saldo,
                    'tiene_email' => !empty($apto->propietario_email),
                ];
            });
        }

        return view('financiero.envio-recibos', compact(
            'edificios', 'apartamentos', 'deudas', 'edificio', 'periodo'
        ));
    }

    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'deudas'   => 'required|array|min:1',
            'deudas.*' => 'exists:cond_deudas_apto,id',
        ]);

        $enviados = 0;
        $errores = [];

        foreach ($validated['deudas'] as $deudaId) {
            $deuda = CondDeudaApto::with(['apartamento.edificio.compania'])->find($deudaId);

            if (!$deuda) {
                continue;
            }

            $apto = $deuda->apartamento;
            $email = $apto->propietario_email;

            if (empty($email)) {
                $errores[] = "Apto {$apto->num_apto}: Sin email registrado";
                continue;
            }

            try {
                Mail::to($email)->send(new ReciboCondominio(
                    deuda: $deuda,
                    propietarioNombre: $apto->propietario_nombre ?? 'Propietario',
                    edificioNombre: $apto->edificio->nombre ?? 'Edificio',
                    companiaNombre: $apto->edificio->compania->nombre ?? 'Administradora',
                ));
                $enviados++;
            } catch (\Exception $e) {
                $errores[] = "Apto {$apto->num_apto}: " . $e->getMessage();
            }
        }

        $mensaje = "Se enviaron {$enviados} recibo(s) exitosamente.";
        if (count($errores) > 0) {
            $mensaje .= " Errores: " . count($errores);
        }

        return redirect()->back()
            ->with('success', $mensaje)
            ->with('errores_envio', $errores);
    }
}
