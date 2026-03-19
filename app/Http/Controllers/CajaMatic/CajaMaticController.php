<?php

namespace App\Http\Controllers\CajaMatic;

use App\Http\Controllers\Controller;
use App\Models\Financiero\CondCaja;
use App\Models\Financiero\CondBanco;
use Illuminate\Http\Request;

class CajaMaticController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:cajamatic.ver')->only(['index', 'disponibilidad']);
        $this->middleware('permission:cajamatic.depositar')->only(['depositar']);
    }

    public function index()
    {
        $cajas = CondCaja::paginate(15);

        return view('financiero.cajamatic', compact('cajas'));
    }

    public function depositar(Request $request)
    {
        return response()->json([
            'message' => 'Deposito registrado exitosamente',
            'module'  => 'CajaMatic',
            'data'    => $request->all(),
        ], 201);
    }

    public function disponibilidad()
    {
        return response()->json([
            'message' => 'Consulta de disponibilidad',
            'module'  => 'CajaMatic',
            'data'    => [
                'disponible_caja'  => 0,
                'disponible_banco' => 0,
            ],
        ]);
    }
}
