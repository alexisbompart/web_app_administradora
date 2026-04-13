<?php

namespace App\Http\Controllers;

use App\Mail\SolicitudServicioConfirmacion;
use App\Models\SolicitudServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SolicitudServicioPublicaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombres_apellidos' => 'required|string|max:150',
            'email'             => 'required|email|max:150',
            'telefono'          => 'required|string|max:30',
            'asunto'            => 'required|string|max:200',
            'descripcion'       => 'nullable|string|max:2000',
        ]);

        $solicitud = SolicitudServicio::create($request->only(
            'nombres_apellidos', 'email', 'telefono', 'asunto', 'descripcion'
        ));

        try {
            Mail::to($solicitud->email)->send(new SolicitudServicioConfirmacion($solicitud));
        } catch (\Exception $e) {
            // El registro fue guardado; el correo es best-effort
        }

        return redirect()->route('home')->with('solicitud_enviada', true);
    }
}
