<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Personal\Trabajador;
use App\Models\Personal\Vacacion;
use Illuminate\Http\Request;

class VacacionController extends Controller
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
        $vacaciones = Vacacion::with('trabajador')->paginate(15);

        return view('personal.vacaciones', compact('vacaciones'));
    }

    public function create()
    {
        $trabajadores = Trabajador::where('estatus', 'A')->get();

        return view('personal.vacaciones-form', compact('trabajadores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id'        => 'required|exists:trabajadores,id',
            'periodo_desde'        => 'required|date',
            'periodo_hasta'        => 'required|date|after_or_equal:periodo_desde',
            'dias_correspondientes' => 'required|integer|min:0',
            'dias_disfrutados'     => 'nullable|integer|min:0',
            'fecha_salida'         => 'nullable|date',
            'fecha_reincorporacion' => 'nullable|date|after_or_equal:fecha_salida',
            'suplente_id'          => 'nullable|exists:trabajadores,id',
            'monto_bono_vacacional' => 'nullable|numeric|min:0',
            'estatus'              => 'nullable|in:pendiente,aprobada,rechazada,disfrutada',
        ]);

        $validated['dias_pendientes'] = ($validated['dias_correspondientes'] ?? 0) - ($validated['dias_disfrutados'] ?? 0);

        Vacacion::create($validated);

        return redirect()->route('personal.vacaciones.index')
            ->with('success', 'Vacacion creada exitosamente.');
    }

    public function show(Vacacion $vacacion)
    {
        $vacacion->load(['trabajador', 'suplente', 'aprobadoPor']);

        return view('personal.vacaciones-show', compact('vacacion'));
    }

    public function edit(Vacacion $vacacion)
    {
        $trabajadores = Trabajador::where('estatus', 'A')->get();

        return view('personal.vacaciones-form', compact('vacacion', 'trabajadores'));
    }

    public function update(Request $request, Vacacion $vacacion)
    {
        $validated = $request->validate([
            'trabajador_id'        => 'required|exists:trabajadores,id',
            'periodo_desde'        => 'required|date',
            'periodo_hasta'        => 'required|date|after_or_equal:periodo_desde',
            'dias_correspondientes' => 'required|integer|min:0',
            'dias_disfrutados'     => 'nullable|integer|min:0',
            'fecha_salida'         => 'nullable|date',
            'fecha_reincorporacion' => 'nullable|date|after_or_equal:fecha_salida',
            'suplente_id'          => 'nullable|exists:trabajadores,id',
            'monto_bono_vacacional' => 'nullable|numeric|min:0',
            'estatus'              => 'nullable|in:pendiente,aprobada,rechazada,disfrutada',
        ]);

        $validated['dias_pendientes'] = ($validated['dias_correspondientes'] ?? 0) - ($validated['dias_disfrutados'] ?? 0);

        $vacacion->update($validated);

        return redirect()->route('personal.vacaciones.show', $vacacion)
            ->with('success', 'Vacacion actualizada exitosamente.');
    }

    public function destroy(Vacacion $vacacion)
    {
        $vacacion->delete();

        return redirect()->route('personal.vacaciones.index')
            ->with('success', 'Vacacion eliminada exitosamente.');
    }
}
