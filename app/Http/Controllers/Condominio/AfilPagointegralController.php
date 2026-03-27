<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Financiero\Banco;
use App\Models\Catalogo\Estado;
use Illuminate\Http\Request;

class AfilPagointegralController extends Controller
{
    public function index()
    {
        $registros = Afilpagointegral::with(['afilapto', 'banco', 'estado'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('condominio.afilpagointegral', compact('registros'));
    }

    public function create()
    {
        $afilaptos = Afilapto::with('apartamento')->orderBy('id')->get();
        $bancos = Banco::orderBy('nombre')->get();
        $estados = Estado::orderBy('nombre')->get();

        return view('condominio.afilpagointegral-form', compact('afilaptos', 'bancos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        Afilpagointegral::create($request->all());

        return redirect()->route('condominio.afilpagointegral.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function edit(Afilpagointegral $afilpagointegral)
    {
        $afilaptos = Afilapto::with('apartamento')->orderBy('id')->get();
        $bancos = Banco::orderBy('nombre')->get();
        $estados = Estado::orderBy('nombre')->get();

        return view('condominio.afilpagointegral-form', compact('afilpagointegral', 'afilaptos', 'bancos', 'estados'));
    }

    public function update(Request $request, Afilpagointegral $afilpagointegral)
    {
        $request->validate($this->rules());

        $afilpagointegral->update($request->all());

        return redirect()->route('condominio.afilpagointegral.index')
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Afilpagointegral $afilpagointegral)
    {
        $afilpagointegral->delete();

        return redirect()->route('condominio.afilpagointegral.index')
            ->with('success', 'Registro eliminado exitosamente');
    }

    private function rules(): array
    {
        return [
            'afilapto_id'    => 'nullable|integer',
            'fecha'          => 'nullable|date',
            'letra'          => 'nullable|string|max:1',
            'cedula_rif'     => 'nullable|string|max:20',
            'nombres'        => 'nullable|string|max:255',
            'apellidos'      => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'email_alterno'  => 'nullable|email|max:255',
            'calle_avenida'  => 'nullable|string|max:255',
            'piso_apto'      => 'nullable|string|max:50',
            'edif_casa'      => 'nullable|string|max:255',
            'urbanizacion'   => 'nullable|string|max:255',
            'ciudad'         => 'nullable|string|max:100',
            'estado_id'      => 'nullable|exists:estados,id',
            'telefono'       => 'nullable|string|max:30',
            'fax'            => 'nullable|string|max:30',
            'celular'        => 'nullable|string|max:30',
            'otro'           => 'nullable|string|max:30',
            'banco_id'       => 'nullable|exists:bancos,id',
            'cta_bancaria'   => 'nullable|string|max:30',
            'tipo_cta'       => 'nullable|string|max:5',
            'nom_usuario'    => 'nullable|string|max:100',
            'clave'          => 'nullable|string|max:100',
            'estatus'        => 'nullable|in:A,D,T,R,P',
            'fecha_estatus'  => 'nullable|date',
            'observaciones'  => 'nullable|string|max:500',
        ];
    }
}
