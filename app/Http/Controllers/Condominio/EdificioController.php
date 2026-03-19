<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.ver-dashboard');
    }

    public function index()
    {
        $edificios = Edificio::with('compania')->paginate(15);

        return view('condominio.edificios', compact('edificios'));
    }

    public function create()
    {
        $companias = Compania::where('activo', true)->get();

        return view('condominio.edificios-form', compact('companias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cod_edif'                 => 'required|string|max:20|unique:cond_edificios,cod_edif',
            'compania_id'              => 'required|exists:cond_companias,id',
            'nombre'                   => 'required|string|max:255',
            'direccion'                => 'nullable|string|max:500',
            'ciudad'                   => 'nullable|string|max:100',
            'telefono'                 => 'nullable|string|max:20',
            'email'                    => 'nullable|email|max:255',
            'total_aptos'              => 'nullable|integer|min:0',
            'rif'                      => 'nullable|string|max:20',
            'alicuota_base'            => 'nullable|numeric|min:0',
            'fondo_reserva_porcentaje' => 'nullable|numeric|min:0|max:100',
            'dia_corte'                => 'nullable|integer|min:1|max:31',
            'dia_vencimiento'          => 'nullable|integer|min:1|max:31',
            'mora_porcentaje'          => 'nullable|numeric|min:0|max:100',
            'activo'                   => 'boolean',
            'latitud'                  => 'nullable|numeric|between:-90,90',
            'longitud'                 => 'nullable|numeric|between:-180,180',
        ]);

        Edificio::create($request->all());

        return redirect()->route('condominio.edificios.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function show(Edificio $edificio)
    {
        $edificio->load(['compania', 'apartamentos']);

        return view('condominio.edificios-show', compact('edificio'));
    }

    public function edit(Edificio $edificio)
    {
        $companias = Compania::where('activo', true)->get();

        return view('condominio.edificios-form', compact('edificio', 'companias'));
    }

    public function update(Request $request, Edificio $edificio)
    {
        $request->validate([
            'cod_edif'                 => 'required|string|max:20|unique:cond_edificios,cod_edif,' . $edificio->id,
            'compania_id'              => 'required|exists:cond_companias,id',
            'nombre'                   => 'required|string|max:255',
            'direccion'                => 'nullable|string|max:500',
            'ciudad'                   => 'nullable|string|max:100',
            'telefono'                 => 'nullable|string|max:20',
            'email'                    => 'nullable|email|max:255',
            'total_aptos'              => 'nullable|integer|min:0',
            'rif'                      => 'nullable|string|max:20',
            'alicuota_base'            => 'nullable|numeric|min:0',
            'fondo_reserva_porcentaje' => 'nullable|numeric|min:0|max:100',
            'dia_corte'                => 'nullable|integer|min:1|max:31',
            'dia_vencimiento'          => 'nullable|integer|min:1|max:31',
            'mora_porcentaje'          => 'nullable|numeric|min:0|max:100',
            'activo'                   => 'boolean',
            'latitud'                  => 'nullable|numeric|between:-90,90',
            'longitud'                 => 'nullable|numeric|between:-180,180',
        ]);

        $edificio->update($request->all());

        return redirect()->route('condominio.edificios.show', $edificio)
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Edificio $edificio)
    {
        $edificio->delete();

        return redirect()->route('condominio.edificios.index')
            ->with('success', 'Registro eliminado exitosamente');
    }
}
