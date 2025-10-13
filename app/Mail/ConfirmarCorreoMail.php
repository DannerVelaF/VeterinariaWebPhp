<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmarCorreoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $code;
    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        // Generar codigo de verificacion
        $this->code = $code;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Codigo de verificación | VeterinariaWeb',
        );
    }

    public function build()
    {
        return $this->subject("Confirmación de correo")
            ->view('mails.confirmarCorreo')
            ->with([
                'code' => $this->code,
            ])
            ->withSwiftMessage(function ($message) {
                $message->embed(public_path('images/logo.jpg'));
            });
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
