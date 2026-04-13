<?php

namespace App\Mail;

use App\Models\SolicitudServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudServicioRespuesta extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SolicitudServicio $solicitud,
        public string $cuerpoMensaje
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a su Solicitud — ' . $this->solicitud->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-respuesta',
        );
    }
}
