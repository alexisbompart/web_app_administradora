<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Propietario;
use Illuminate\Http\Request;

class PropietarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.ver-dashboard');
    }

    public function index()
    {
        $propietarios = Propietario::paginate(15);

        return view('condominio.propietarios', compact('propietarios'));
    }

    public function create()
    {
        return view('condominio.propietarios-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:propietarios,cedula',
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'estatus'   => 'boolean',
        ]);

        Propietario::create($request->all());

        return redirect()->route('condominio.propietarios.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function show(Propietario $propietario)
    {
        $propietario->load(['apartamentos.edificio', 'apartamentos.condDeudasApto']);

        return view('condominio.propietarios-show', compact('propietario'));
    }

    public function edit(Propietario $propietario)
    {
        return view('condominio.propietarios-form', compact('propietario'));
    }

    public function update(Request $request, Propietario $propietario)
    {
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:propietarios,cedula,' . $propietario->id,
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'estatus'   => 'boolean',
        ]);

        $propietario->update($request->all());

        return redirect()->route('condominio.propietarios.show', $propietario)
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Propietario $propietario)
    {
        $propietario->delete();

        return redirect()->route('condominio.propietarios.index')
            ->with('success', 'Registro eliminado exitosamente');
    }
}
