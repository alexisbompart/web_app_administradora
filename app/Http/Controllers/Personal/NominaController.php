<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use App\Models\Personal\Nomina;
use App\Models\Personal\Trabajador;
use Illuminate\Http\Request;

class NominaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:personal.ver')->only(['index', 'show']);
        $this->middleware('permission:personal.crear')->only(['create', 'store']);
        $this->middleware('permission:personal.editar')->only(['edit', 'update']);
        $this->middleware('permission:personal.eliminar')->only(['destroy']);
        $this->middleware('permission:personal.procesar-nomina')->only(['procesar']);
        $this->middleware('permission:personal.aprobar-nomina')->only(['aprobar']);
    }

    public function index()
    {
        $nominas = Nomina::paginate(15);

        return view('personal.nominas', compact('nominas'));
    }

    public function create()
    {
        $companias = Compania::where('activo', true)->get();
        $trabajadores = Trabajador::where('estatus', 'A')->get();

        return view('personal.nominas-form', compact('companias', 'trabajadores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'compania_id'    => 'required|exists:cond_companias,id',
            'codigo'         => 'required|string|max:50|unique:nominas,codigo',
            'periodo_inicio' => 'required|date',
            'periodo_fin'    => 'required|date|after_or_equal:periodo_inicio',
            'tipo'           => 'required|in:quincenal,mensual,especial',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        $validated['estatus'] = 'borrador';

        Nomina::create($validated);

        return redirect()->route('personal.nominas.index')
            ->with('success', 'Nomina creada exitosamente.');
    }

    public function show(Nomina $nomina)
    {
        $nomina->load(['compania', 'nominaDetalles.trabajador', 'procesadoPor']);

        return view('personal.nominas-show', compact('nomina'));
    }

    public function edit(Nomina $nomina)
    {
        $companias = Compania::where('activo', true)->get();
        $trabajadores = Trabajador::where('estatus', 'A')->get();

        return view('personal.nominas-form', compact('nomina', 'companias', 'trabajadores'));
    }

    public function update(Request $request, Nomina $nomina)
    {
        $validated = $request->validate([
            'compania_id'    => 'required|exists:cond_companias,id',
            'codigo'         => 'required|string|max:50|unique:nominas,codigo,' . $nomina->id,
            'periodo_inicio' => 'required|date',
            'periodo_fin'    => 'required|date|after_or_equal:periodo_inicio',
            'tipo'           => 'required|in:quincenal,mensual,especial',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        $nomina->update($validated);

        return redirect()->route('personal.nominas.show', $nomina)
            ->with('success', 'Nomina actualizada exitosamente.');
    }

    public function destroy(Nomina $nomina)
    {
        $nomina->delete();

        return redirect()->route('personal.nominas.index')
            ->with('success', 'Nomina eliminada exitosamente.');
    }

    public function procesar(Nomina $nomina)
    {
        $nomina->update([
            'estatus'              => 'procesada',
            'fecha_procesamiento'  => now(),
            'procesado_por'        => auth()->id(),
        ]);

        return redirect()->route('personal.nominas.show', $nomina)
            ->with('success', 'Nomina procesada exitosamente.');
    }

    public function aprobar(Nomina $nomina)
    {
        $nomina->update([
            'estatus' => 'pagada',
        ]);

        return redirect()->route('personal.nominas.show', $nomina)
            ->with('success', 'Nomina aprobada exitosamente.');
    }
}
