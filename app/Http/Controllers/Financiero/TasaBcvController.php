<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Catalogo\TasaBcv;
use Illuminate\Http\Request;

class TasaBcvController extends Controller
{
    public function index(Request $request)
    {
        $query = TasaBcv::where('moneda', 'USD')->orderByDesc('fecha');

        if ($request->filled('mes')) {
            $query->whereRaw("TO_CHAR(fecha, 'YYYY-MM') = ?", [$request->mes]);
        }

        $tasas = $query->paginate(30)->withQueryString();
        $tasaHoy = TasaBcv::where('moneda', 'USD')->where('fecha', '<=', now()->toDateString())
            ->orderByDesc('fecha')->first();

        return view('financiero.tasabcv', compact('tasas', 'tasaHoy'));
    }

    public function create()
    {
        return view('financiero.tasabcv-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'tasa'  => 'required|numeric|min:0.0001',
        ]);

        TasaBcv::updateOrCreate(
            ['fecha' => $validated['fecha'], 'moneda' => 'USD'],
            ['tasa' => $validated['tasa'], 'fuente' => 'BCV']
        );

        return redirect()->route('financiero.tasabcv.index')
            ->with('success', 'Tasa registrada exitosamente.');
    }

    public function edit(TasaBcv $tasabcv)
    {
        return view('financiero.tasabcv-form', ['tasa' => $tasabcv]);
    }

    public function update(Request $request, TasaBcv $tasabcv)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'tasa'  => 'required|numeric|min:0.0001',
        ]);

        $tasabcv->update([
            'fecha'  => $validated['fecha'],
            'tasa'   => $validated['tasa'],
            'fuente' => 'BCV',
        ]);

        return redirect()->route('financiero.tasabcv.index')
            ->with('success', 'Tasa actualizada exitosamente.');
    }

    public function destroy(TasaBcv $tasabcv)
    {
        $tasabcv->delete();

        return redirect()->route('financiero.tasabcv.index')
            ->with('success', 'Tasa eliminada.');
    }
}
