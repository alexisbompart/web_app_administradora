<?php

namespace App\Mail;

use App\Models\SolicitudServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudServicioConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SolicitudServicio $solicitud
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de Servicio Recibida — ' . $this->solicitud->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-confirmacion',
        );
    }
}
