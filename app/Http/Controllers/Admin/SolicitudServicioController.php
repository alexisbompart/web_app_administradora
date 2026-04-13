<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SolicitudServicioRespuesta;
use App\Models\SolicitudServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SolicitudServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = SolicitudServicio::latest();

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres_apellidos', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('asunto', 'like', "%{$buscar}%");
            });
        }

        $solicitudes = $query->paginate(20)->withQueryString();

        $totales = [
            'total'       => SolicitudServicio::count(),
            'pendiente'   => SolicitudServicio::where('estatus', 'pendiente')->count(),
            'en_revision' => SolicitudServicio::where('estatus', 'en_revision')->count(),
            'respondida'  => SolicitudServicio::where('estatus', 'respondida')->count(),
            'cerrada'     => SolicitudServicio::where('estatus', 'cerrada')->count(),
        ];

        return view('admin.solicitudes-servicio', compact('solicitudes', 'totales'));
    }

    public function updateEstatus(Request $request, SolicitudServicio $solicitud)
    {
        $request->validate([
            'estatus' => 'required|in:pendiente,en_revision,respondida,cerrada',
        ]);

        $solicitud->update([
            'estatus'      => $request->estatus,
            'atendido_por' => auth()->id(),
        ]);

        return back()->with('success', 'Estatus actualizado correctamente.');
    }

    public function guardarNotas(Request $request, SolicitudServicio $solicitud)
    {
        $request->validate([
            'notas_internas' => 'nullable|string|max:2000',
        ]);

        $solicitud->update([
            'notas_internas' => $request->notas_internas,
            'atendido_por'   => auth()->id(),
        ]);

        return back()->with('success', 'Notas guardadas.');
    }

    public function enviarCorreo(Request $request, SolicitudServicio $solicitud)
    {
        $request->validate([
            'cuerpo_mensaje' => 'required|string|min:10|max:5000',
        ]);

        $correoEnviado = true;
        $errorCorreo   = null;

        try {
            Mail::to($solicitud->email)->send(
                new SolicitudServicioRespuesta($solicitud, $request->cuerpo_mensaje)
            );
        } catch (\Exception $e) {
            $correoEnviado = false;
            $errorCorreo   = $e->getMessage();
        }

        $solicitud->update([
            'estatus'         => 'respondida',
            'fecha_respuesta' => now(),
            'atendido_por'    => auth()->id(),
        ]);

        if ($correoEnviado) {
            return back()->with('success', "Correo enviado a {$solicitud->email} y solicitud marcada como Respondida.");
        }

        return back()->with('warning', "La solicitud fue marcada como Respondida, pero el correo no pudo enviarse ({$errorCorreo}). Verifique la configuración de correo.");
    }
}
