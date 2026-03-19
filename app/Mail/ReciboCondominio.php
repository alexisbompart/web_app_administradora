<?php

namespace App\Mail;

use App\Models\Financiero\CondDeudaApto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReciboCondominio extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CondDeudaApto $deuda,
        public string $propietarioNombre,
        public string $edificioNombre,
        public string $companiaNombre,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Recibo de Condominio - Periodo {$this->deuda->periodo} - {$this->edificioNombre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recibo-condominio',
        );
    }
}
