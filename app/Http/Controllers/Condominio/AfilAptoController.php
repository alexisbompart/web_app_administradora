<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Http\Request;

class AfilAptoController extends Controller
{
    public function index()
    {
        $afilaptos = Afilapto::with(['edificio', 'apartamento', 'compania', 'afilpagointegral'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('condominio.afilapto', compact('afilaptos'));
    }

    public function create()
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $companias = Compania::orderBy('nombre')->get();

        return view('condominio.afilapto-form', compact('edificios', 'companias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'edificio_id'     => 'nullable|exists:cond_edificios,id',
            'apartamento_id'  => 'nullable|exists:cond_aptos,id',
            'compania_id'     => 'nullable|exists:cond_companias,id',
            'estatus_afil'    => 'required|in:A,D,P',
            'fecha_afiliacion' => 'nullable|date',
            'observaciones'   => 'nullable|string|max:500',
        ]);

        Afilapto::create($request->all());

        return redirect()->route('condominio.afilapto.index')
            ->with('success', 'Afiliacion creada exitosamente');
    }

    public function edit(Afilapto $afilapto)
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $companias = Compania::orderBy('nombre')->get();
        $apartamentos = $afilapto->edificio_id
            ? Apartamento::where('edificio_id', $afilapto->edificio_id)->orderBy('num_apto')->get()
            : collect();

        return view('condominio.afilapto-form', compact('afilapto', 'edificios', 'companias', 'apartamentos'));
    }

    public function update(Request $request, Afilapto $afilapto)
    {
        $request->validate([
            'edificio_id'     => 'nullable|exists:cond_edificios,id',
            'apartamento_id'  => 'nullable|exists:cond_aptos,id',
            'compania_id'     => 'nullable|exists:cond_companias,id',
            'estatus_afil'    => 'required|in:A,D,P',
            'fecha_afiliacion' => 'nullable|date',
            'observaciones'   => 'nullable|string|max:500',
        ]);

        $afilapto->update($request->all());

        return redirect()->route('condominio.afilapto.index')
            ->with('success', 'Afiliacion actualizada exitosamente');
    }

    public function destroy(Afilapto $afilapto)
    {
        $afilapto->afilpagointegral?->delete();
        $afilapto->delete();

        return redirect()->route('condominio.afilapto.index')
            ->with('success', 'Afiliacion eliminada exitosamente');
    }
}
