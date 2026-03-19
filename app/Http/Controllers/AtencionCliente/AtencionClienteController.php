<?php

namespace App\Http\Controllers\AtencionCliente;

use App\Http\Controllers\Controller;
use App\Models\AtencionCliente;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Propietario;
use Illuminate\Http\Request;

class AtencionClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:atencion-cliente.ver')->only(['index', 'show']);
        $this->middleware('permission:atencion-cliente.crear')->only(['create', 'store']);
        $this->middleware('permission:atencion-cliente.editar')->only(['edit', 'update']);
        $this->middleware('permission:atencion-cliente.eliminar')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = AtencionCliente::with(['edificio', 'propietario', 'ejecutivo']);

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $solicitudes = $query->latest()->paginate(15)->withQueryString();

        return view('atencion-cliente.index', compact('solicitudes'));
    }

    public function create()
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $propietarios = Propietario::where('estatus', true)->orderBy('nombres')->get();

        return view('atencion-cliente.form', compact('edificios', 'propietarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'edificio_id'    => 'nullable|exists:cond_edificios,id',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'tipo'           => 'required|string|in:consulta,queja,solicitud,emergencia,asesoria_legal,asamblea',
            'asunto'         => 'required|string|max:200',
            'descripcion'    => 'nullable|string',
            'prioridad'      => 'required|string|in:baja,media,alta,urgente',
        ]);

        $edificio = $validated['edificio_id'] ? Edificio::find($validated['edificio_id']) : null;

        AtencionCliente::create([
            'compania_id'    => $edificio?->compania_id,
            'edificio_id'    => $validated['edificio_id'],
            'propietario_id' => $validated['propietario_id'],
            'ejecutivo_id'   => auth()->id(),
            'tipo'           => $validated['tipo'],
            'asunto'         => $validated['asunto'],
            'descripcion'    => $validated['descripcion'],
            'prioridad'      => $validated['prioridad'],
            'estatus'        => 'abierto',
            'fecha_apertura' => now(),
        ]);

        return redirect()->route('servicios.atencion-cliente.index')
            ->with('success', 'Solicitud creada exitosamente.');
    }

    public function show(AtencionCliente $atencionCliente)
    {
        $atencionCliente->load(['edificio', 'propietario', 'ejecutivo', 'compania']);

        return view('atencion-cliente.show', ['solicitud' => $atencionCliente]);
    }

    public function edit(AtencionCliente $atencionCliente)
    {
        $edificios = Edificio::orderBy('nombre')->get();
        $propietarios = Propietario::where('estatus', true)->orderBy('nombres')->get();

        return view('atencion-cliente.form', [
            'solicitud'     => $atencionCliente,
            'edificios'     => $edificios,
            'propietarios'  => $propietarios,
        ]);
    }

    public function update(Request $request, AtencionCliente $atencionCliente)
    {
        $validated = $request->validate([
            'edificio_id'    => 'nullable|exists:cond_edificios,id',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'tipo'           => 'required|string|in:consulta,queja,solicitud,emergencia,asesoria_legal,asamblea',
            'asunto'         => 'required|string|max:200',
            'descripcion'    => 'nullable|string',
            'prioridad'      => 'required|string|in:baja,media,alta,urgente',
            'estatus'        => 'required|string|in:abierto,en_proceso,resuelto,cerrado',
            'respuesta'      => 'nullable|string',
        ]);

        $edificio = $validated['edificio_id'] ? Edificio::find($validated['edificio_id']) : null;
        $validated['compania_id'] = $edificio?->compania_id;

        if ($validated['estatus'] === 'cerrado' && $atencionCliente->estatus !== 'cerrado') {
            $validated['fecha_cierre'] = now();
        }

        $atencionCliente->update($validated);

        return redirect()->route('servicios.atencion-cliente.index')
            ->with('success', 'Solicitud actualizada exitosamente.');
    }

    public function destroy(AtencionCliente $atencionCliente)
    {
        $atencionCliente->delete();

        return redirect()->route('servicios.atencion-cliente.index')
            ->with('success', 'Solicitud eliminada exitosamente.');
    }
}
