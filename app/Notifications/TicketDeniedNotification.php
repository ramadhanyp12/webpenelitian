<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketDeniedNotification extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tiket Ditolak')
            ->greeting('Halo '.$this->ticket->user->name.',')
            ->line('Maaf, tiket kamu ditolak.')
            ->line('Judul: '.$this->ticket->judul_penelitian)
            ->line('Alasan: '.($this->ticket->alasan_ditolak ?? '-'))
            ->action('Lihat Tiket', route('tickets.show', $this->ticket->id))
            ->line('Silakan perbaiki sesuai catatan di atas.');
    }
}
