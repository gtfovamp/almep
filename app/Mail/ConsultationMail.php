<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Consultation $consultation;

    public function __construct(Consultation $consultation)
    {
        $this->consultation = $consultation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая заявка на консультацию — ' . $this->consultation->name,
            replyTo: $this->consultation->email
                ? [new \Illuminate\Mail\Mailables\Address($this->consultation->email, $this->consultation->name)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation',
            with: ['item' => $this->consultation],
        );
    }
}
