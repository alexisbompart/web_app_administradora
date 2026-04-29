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
    public function index(Request $request)
    {
        $filtro      = $request->input('filtro');
        $edificioId  = $request->input('edificio_id');
        $buscarApto  = $request->input('apto');
        $buscarPint  = $request->input('pint');
        $estatus     = $request->input('estatus');

        $query = Afilapto::with(['edificio', 'apartamento', 'compania', 'afilpagointegral']);

        // Filtros de incompletos (tarjetas de alerta)
        if ($filtro === 'sin_apto') {
            $query->whereNull('apartamento_id');
        } elseif ($filtro === 'sin_edificio') {
            $query->whereNull('edificio_id');
        } elseif ($filtro === 'incompletos') {
            $query->where(fn($q) => $q->whereNull('apartamento_id')->orWhereNull('edificio_id'));
        }

        // Filtros de búsqueda
        if ($edificioId) {
            $query->where('edificio_id', $edificioId);
        }
        if ($buscarApto) {
            $query->whereHas('apartamento', fn($q) => $q->where('num_apto', 'ilike', "%{$buscarApto}%"));
        }
        if ($buscarPint) {
            $query->where('cod_pint', 'ilike', "%{$buscarPint}%");
        }
        if ($estatus) {
            $query->where('estatus_afil', $estatus);
        }

        $afilaptos = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $sinApto     = Afilapto::whereNull('apartamento_id')->count();
        $sinEdificio = Afilapto::whereNull('edificio_id')->count();
        $edificios   = Edificio::orderBy('nombre')->get(['id', 'nombre', 'cod_edif']);

        return view('condominio.afilapto', compact(
            'afilaptos', 'sinApto', 'sinEdificio', 'filtro',
            'edificioId', 'buscarApto', 'buscarPint', 'estatus', 'edificios'
        ));
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

        return redirect()->route('condominio.afiliaciones-apto.index')
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

        return redirect()->route('condominio.afiliaciones-apto.index')
            ->with('success', 'Afiliacion actualizada exitosamente');
    }

    public function destroy(Afilapto $afilapto)
    {
        $afilapto->delete();

        return redirect()->route('condominio.afiliaciones-apto.index')
            ->with('success', 'Afiliacion eliminada exitosamente');
    }
}
