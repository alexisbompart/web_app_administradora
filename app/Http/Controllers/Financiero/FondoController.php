<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use App\Models\Financiero\Fondo;
use App\Models\Financiero\MovimientoFondo;
use Illuminate\Http\Request;

class FondoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:fondos.ver')->only(['index', 'show']);
        $this->middleware('permission:fondos.crear')->only(['create', 'store']);
        $this->middleware('permission:fondos.editar')->only(['edit', 'update']);
        $this->middleware('permission:fondos.eliminar')->only(['destroy']);
        $this->middleware('permission:fondos.registrar-movimiento')->only(['registrarMovimiento']);
    }

    public function index()
    {
        $fondos = Fondo::all();
        $movimientos = MovimientoFondo::with('fondo')->latest()->paginate(15);

        return view('financiero.fondos', compact('fondos', 'movimientos'));
    }

    public function create()
    {
        $companias = Compania::where('activo', true)->get();

        return view('financiero.fondos-form', compact('companias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'compania_id' => 'required|exists:companias,id',
            'nombre'      => 'required|string|max:255',
            'tipo'        => 'required|in:contingencias,prestaciones,reserva,especial',
            'saldo_actual' => 'nullable|numeric|min:0',
            'meta'        => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        Fondo::create($validated);

        return redirect()->route('financiero.fondos.index')
            ->with('success', 'Fondo creado exitosamente.');
    }

    public function show(Fondo $fondo)
    {
        $fondo->load('movimientosFondo', 'compania');

        return view('financiero.fondos-show', compact('fondo'));
    }

    public function edit(Fondo $fondo)
    {
        $companias = Compania::where('activo', true)->get();

        return view('financiero.fondos-form', compact('fondo', 'companias'));
    }

    public function update(Request $request, Fondo $fondo)
    {
        $validated = $request->validate([
            'compania_id' => 'required|exists:companias,id',
            'nombre'      => 'required|string|max:255',
            'tipo'        => 'required|in:contingencias,prestaciones,reserva,especial',
            'saldo_actual' => 'nullable|numeric|min:0',
            'meta'        => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $fondo->update($validated);

        return redirect()->route('financiero.fondos.index')
            ->with('success', 'Fondo actualizado exitosamente.');
    }

    public function destroy(Fondo $fondo)
    {
        $fondo->delete();

        return redirect()->route('financiero.fondos.index')
            ->with('success', 'Fondo eliminado exitosamente.');
    }

    public function registrarMovimiento(Request $request, Fondo $fondo)
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:I,E',
            'monto'           => 'required|numeric|min:0.01',
            'descripcion'     => 'required|string|max:500',
            'referencia'      => 'nullable|string|max:255',
        ]);

        $saldoAnterior = $fondo->saldo_actual;

        if ($validated['tipo_movimiento'] === 'I') {
            $saldoPosterior = $saldoAnterior + $validated['monto'];
        } else {
            $saldoPosterior = $saldoAnterior - $validated['monto'];
        }

        MovimientoFondo::create([
            'fondo_id'         => $fondo->id,
            'tipo_movimiento'  => $validated['tipo_movimiento'],
            'monto'            => $validated['monto'],
            'saldo_anterior'   => $saldoAnterior,
            'saldo_posterior'  => $saldoPosterior,
            'descripcion'      => $validated['descripcion'],
            'referencia'       => $validated['referencia'] ?? null,
            'fecha_movimiento' => now(),
            'registrado_por'   => auth()->id(),
        ]);

        $fondo->update(['saldo_actual' => $saldoPosterior]);

        return redirect()->route('financiero.fondos.show', $fondo)
            ->with('success', 'Movimiento registrado exitosamente.');
    }
}
