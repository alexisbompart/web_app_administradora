<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Financiero\ConcBancaria;
use Illuminate\Http\Request;

class ConcBancariaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:fondos.conciliar');
    }

    public function index()
    {
        $conciliaciones = ConcBancaria::with('condBanco.banco')->paginate(15);

        return view('financiero.conciliaciones', compact('conciliaciones'));
    }

    public function create()
    {
        return response()->json([
            'message' => 'Formulario de creacion de conciliacion bancaria',
            'module'  => 'Financiero',
        ]);
    }

    public function store(Request $request)
    {
        $conciliacion = ConcBancaria::create($request->all());

        return response()->json([
            'message' => 'Conciliacion bancaria creada exitosamente',
            'module'  => 'Financiero',
            'data'    => $conciliacion,
        ], 201);
    }

    public function show(ConcBancaria $concBancaria)
    {
        return response()->json([
            'message' => 'Detalle de la conciliacion bancaria',
            'module'  => 'Financiero',
            'data'    => $concBancaria,
        ]);
    }

    public function edit(ConcBancaria $concBancaria)
    {
        return response()->json([
            'message' => 'Formulario de edicion de conciliacion bancaria',
            'module'  => 'Financiero',
            'data'    => $concBancaria,
        ]);
    }

    public function update(Request $request, ConcBancaria $concBancaria)
    {
        $concBancaria->update($request->all());

        return response()->json([
            'message' => 'Conciliacion bancaria actualizada exitosamente',
            'module'  => 'Financiero',
            'data'    => $concBancaria,
        ]);
    }

    public function destroy(ConcBancaria $concBancaria)
    {
        $concBancaria->delete();

        return response()->json([
            'message' => 'Conciliacion bancaria eliminada exitosamente',
            'module'  => 'Financiero',
        ]);
    }
}
