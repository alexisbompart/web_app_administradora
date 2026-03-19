<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use Illuminate\Http\Request;

class CompaniaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.ver-dashboard');
    }

    public function index()
    {
        $companias = Compania::paginate(15);

        return view('condominio.companias', compact('companias'));
    }

    public function create()
    {
        return view('condominio.companias-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cod_compania' => 'required|string|max:20|unique:cond_companias,cod_compania',
            'nombre'       => 'required|string|max:255',
            'rif'          => 'required|string|max:20',
            'direccion'    => 'nullable|string|max:500',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'activo'       => 'boolean',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        Compania::create($request->all());

        return redirect()->route('condominio.companias.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function show(Compania $compania)
    {
        $compania->load('edificios');

        return view('condominio.companias-show', compact('compania'));
    }

    public function edit(Compania $compania)
    {
        return view('condominio.companias-form', compact('compania'));
    }

    public function update(Request $request, Compania $compania)
    {
        $request->validate([
            'cod_compania' => 'required|string|max:20|unique:cond_companias,cod_compania,' . $compania->id,
            'nombre'       => 'required|string|max:255',
            'rif'          => 'required|string|max:20',
            'direccion'    => 'nullable|string|max:500',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'activo'       => 'boolean',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $compania->update($request->all());

        return redirect()->route('condominio.companias.show', $compania)
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Compania $compania)
    {
        $compania->delete();

        return redirect()->route('condominio.companias.index')
            ->with('success', 'Registro eliminado exitosamente');
    }
}
