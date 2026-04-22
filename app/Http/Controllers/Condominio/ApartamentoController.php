<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use Illuminate\Http\Request;

class ApartamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.ver-dashboard');
    }

    public function index(Request $request)
    {
        $buscar    = $request->input('buscar');
        $edificioId = $request->input('edificio_id');
        $companiaId = $request->input('compania_id');
        $estatus   = $request->input('estatus');

        $apartamentos = Apartamento::with(['edificio.compania'])
            ->when($buscar, fn($q) => $q->where('num_apto', 'ilike', "%{$buscar}%"))
            ->when($edificioId, fn($q) => $q->where('edificio_id', $edificioId))
            ->when($companiaId, fn($q) => $q->whereHas('edificio', fn($q2) => $q2->where('compania_id', $companiaId)))
            ->when($estatus, fn($q) => $q->where('estatus', $estatus))
            ->orderBy('edificio_id')
            ->orderBy('num_apto')
            ->paginate(15)
            ->withQueryString();

        $edificios  = Edificio::orderBy('nombre')->get(['id', 'nombre', 'cod_edif']);
        $companias  = \App\Models\Condominio\Compania::orderBy('nombre')->get(['id', 'nombre']);

        return view('condominio.apartamentos', compact('apartamentos', 'buscar', 'edificioId', 'companiaId', 'estatus', 'edificios', 'companias'));
    }

    public function create()
    {
        $edificios = Edificio::where('activo', true)->get();

        return view('condominio.apartamentos-form', compact('edificios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'edificio_id'          => 'required|exists:cond_edificios,id',
            'num_apto'             => 'required|string|max:20',
            'piso'                 => 'nullable|string|max:10',
            'area_mts'            => 'nullable|numeric|min:0',
            'alicuota'            => 'nullable|numeric|min:0',
            'habitaciones'        => 'nullable|integer|min:0',
            'banos'               => 'nullable|integer|min:0',
            'estacionamiento'     => 'boolean',
            'propietario_nombre'  => 'nullable|string|max:255',
            'propietario_cedula'  => 'nullable|string|max:20',
            'propietario_telefono'=> 'nullable|string|max:20',
            'propietario_email'   => 'nullable|email|max:255',
            'estatus'             => 'required|in:A,I,M',
        ]);

        Apartamento::create($request->all());

        return redirect()->route('condominio.apartamentos.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function show(Apartamento $apartamento)
    {
        $apartamento->load(['edificio', 'propietarios', 'condDeudasApto']);

        return view('condominio.apartamentos-show', compact('apartamento'));
    }

    public function edit(Apartamento $apartamento)
    {
        $edificios = Edificio::where('activo', true)->get();

        return view('condominio.apartamentos-form', compact('apartamento', 'edificios'));
    }

    public function update(Request $request, Apartamento $apartamento)
    {
        $request->validate([
            'edificio_id'          => 'required|exists:cond_edificios,id',
            'num_apto'             => 'required|string|max:20',
            'piso'                 => 'nullable|string|max:10',
            'area_mts'            => 'nullable|numeric|min:0',
            'alicuota'            => 'nullable|numeric|min:0',
            'habitaciones'        => 'nullable|integer|min:0',
            'banos'               => 'nullable|integer|min:0',
            'estacionamiento'     => 'boolean',
            'propietario_nombre'  => 'nullable|string|max:255',
            'propietario_cedula'  => 'nullable|string|max:20',
            'propietario_telefono'=> 'nullable|string|max:20',
            'propietario_email'   => 'nullable|email|max:255',
            'estatus'             => 'required|in:A,I,M',
        ]);

        $apartamento->update($request->all());

        return redirect()->route('condominio.apartamentos.show', $apartamento)
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Apartamento $apartamento)
    {
        $apartamento->delete();

        return redirect()->route('condominio.apartamentos.index')
            ->with('success', 'Registro eliminado exitosamente');
    }
}
