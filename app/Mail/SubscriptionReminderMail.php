<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Kelas Mailable untuk pengiriman email pengingat langganan (NFR-003).
 */
class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Inisialisasi instance pesan email dengan entitas Notifikasi.
     */
    public function __construct(
        public Notification $notification
    ) {
    }

    /**
     * Konfigurasi amplop email (subjek dan pengirim).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notification->title,
        );
    }

    /**
     * Konfigurasi konten tampilan isi email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reminder',
        );
    }
}
