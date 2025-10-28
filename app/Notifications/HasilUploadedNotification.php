<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class HasilUploadedNotification extends Notification
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
            ->subject('User Mengunggah Hasil Penelitian')
            ->greeting('Halo Admin,')
            ->line('User '.$this->ticket->user->name.' telah mengunggah hasil penelitian.')
            ->line('Judul: '.$this->ticket->judul_penelitian)
            ->action('Tinjau', route('admin.tickets.show', $this->ticket->id))
            ->line('Terima kasih.');
    }
}
