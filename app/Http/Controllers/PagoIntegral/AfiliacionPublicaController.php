<?php

namespace App\Http\Controllers\PagoIntegral;

use App\Http\Controllers\Controller;
use App\Models\Catalogo\Estado;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Financiero\Banco;
use Illuminate\Http\Request;

class AfiliacionPublicaController extends Controller
{
    public function show()
    {
        $bancos  = Banco::orderBy('nombre')->get();
        $estados = Estado::orderBy('nombre')->get();

        return view('public.afiliacion', compact('bancos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'      => 'required|string|max:100',
            'apellidos'    => 'required|string|max:100',
            'cedula_rif'   => 'required|string|max:20',
            'letra'        => 'required|in:V,E,J,G,P',
            'email'        => 'required|email|max:100',
            'email_confirmation' => 'required|same:email',
            'cta_bancaria' => 'required|string|max:20',
            'banco_id'     => 'required|exists:bancos,id',
            'tipo_cta'     => 'nullable|string|max:20',
            'telefono'     => 'nullable|string|max:20',
            'celular'      => 'nullable|string|max:20',
            'fax'          => 'nullable|string|max:20',
            'otro'         => 'nullable|string|max:20',
            'calle_avenida'  => 'nullable|string|max:200',
            'edif_casa'      => 'nullable|string|max:100',
            'ciudad'         => 'nullable|string|max:100',
            'estado_id'      => 'nullable|exists:estados,id',
            'piso_apto'      => 'nullable|string|max:50',
            'urbanizacion'   => 'nullable|string|max:100',
            'email_alterno'  => 'nullable|email|max:100',
            'nom_usuario'    => 'nullable|string|max:100',
            'pint'           => 'nullable|array|max:4',
            'pint.*'         => 'nullable|string|max:20',
        ]);

        $pintCodes = array_filter($request->input('pint', []));

        Afilpagointegral::create([
            'nombres'       => $request->nombres,
            'apellidos'     => $request->apellidos,
            'cedula_rif'    => $request->cedula_rif,
            'letra'         => $request->letra,
            'email'         => $request->email,
            'email_alterno' => $request->email_alterno,
            'cta_bancaria'  => $request->cta_bancaria,
            'banco_id'      => $request->banco_id,
            'tipo_cta'      => $request->tipo_cta,
            'telefono'      => $request->telefono,
            'celular'       => $request->celular,
            'fax'           => $request->fax,
            'otro'          => $request->otro,
            'calle_avenida' => $request->calle_avenida,
            'edif_casa'     => $request->edif_casa,
            'ciudad'        => $request->ciudad,
            'estado_id'     => $request->estado_id,
            'piso_apto'     => $request->piso_apto,
            'urbanizacion'  => $request->urbanizacion,
            'nom_usuario'   => $request->nom_usuario,
            'fecha'         => now()->toDateString(),
            'estatus'       => 'P',
            'creado_por'    => 'WEB',
            'observaciones' => !empty($pintCodes) ? json_encode(['pint' => array_values($pintCodes)]) : null,
        ]);

        return redirect()->back()->with('success', 'Su solicitud de afiliacion ha sido registrada. Un administrador revisara su informacion y le notificara por correo electronico.');
    }
}
