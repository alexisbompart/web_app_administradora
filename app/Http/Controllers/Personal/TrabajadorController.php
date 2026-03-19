<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use App\Models\Personal\Trabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:personal.ver')->only(['index', 'show']);
        $this->middleware('permission:personal.crear')->only(['create', 'store']);
        $this->middleware('permission:personal.editar')->only(['edit', 'update']);
        $this->middleware('permission:personal.eliminar')->only(['destroy']);
    }

    public function index()
    {
        $trabajadores = Trabajador::paginate(15);

        return view('personal.trabajadores', compact('trabajadores'));
    }

    public function create()
    {
        $companias = Compania::where('activo', true)->get();

        return view('personal.trabajadores-form', compact('companias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'compania_id'      => 'nullable|exists:cond_companias,id',
            'cedula'           => 'required|string|max:20|unique:trabajadores,cedula',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'direccion'        => 'nullable|string|max:500',
            'telefono'         => 'nullable|string|max:20',
            'celular'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'cargo'            => 'required|string|max:100',
            'departamento'     => 'nullable|string|max:100',
            'fecha_ingreso'    => 'required|date',
            'salario_basico'   => 'required|numeric|min:0',
            'tipo_contrato'    => 'nullable|in:fijo,temporal,pasante',
            'estatus'          => 'nullable|in:A,I,V',
        ]);

        Trabajador::create($validated);

        return redirect()->route('personal.trabajadores.index')
            ->with('success', 'Trabajador creado exitosamente.');
    }

    public function show(Trabajador $trabajador)
    {
        $trabajador->load(['compania', 'nominaDetalles.nomina', 'prestacionesSociales', 'vacaciones']);

        return view('personal.trabajadores-show', compact('trabajador'));
    }

    public function edit(Trabajador $trabajador)
    {
        $companias = Compania::where('activo', true)->get();

        return view('personal.trabajadores-form', compact('trabajador', 'companias'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'compania_id'      => 'nullable|exists:cond_companias,id',
            'cedula'           => 'required|string|max:20|unique:trabajadores,cedula,' . $trabajador->id,
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'direccion'        => 'nullable|string|max:500',
            'telefono'         => 'nullable|string|max:20',
            'celular'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'cargo'            => 'required|string|max:100',
            'departamento'     => 'nullable|string|max:100',
            'fecha_ingreso'    => 'required|date',
            'salario_basico'   => 'required|numeric|min:0',
            'tipo_contrato'    => 'nullable|in:fijo,temporal,pasante',
            'estatus'          => 'nullable|in:A,I,V',
        ]);

        $trabajador->update($validated);

        return redirect()->route('personal.trabajadores.show', $trabajador)
            ->with('success', 'Trabajador actualizado exitosamente.');
    }

    public function destroy(Trabajador $trabajador)
    {
        $trabajador->delete();

        return redirect()->route('personal.trabajadores.index')
            ->with('success', 'Trabajador eliminado exitosamente.');
    }
}
