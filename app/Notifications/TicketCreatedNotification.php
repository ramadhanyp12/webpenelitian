<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
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
            ->subject('Tiket Baru: '.$this->ticket->judul_penelitian)
            ->greeting('Halo Admin,')
            ->line('Ada tiket baru dibuat oleh: '.$this->ticket->user->name)
            ->line('Judul: '.$this->ticket->judul_penelitian)
            ->line('Status: '.$this->ticket->status)
            ->action('Buka di Admin', route('admin.tickets.show', $this->ticket->id))
            ->line('Terima kasih.');
    }
}
