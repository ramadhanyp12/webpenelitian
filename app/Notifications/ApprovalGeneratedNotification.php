<?php

namespace App\Notifications;

use App\Models\ApprovalDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalGeneratedNotification extends Notification
{
    use Queueable;

    public function __construct(public ApprovalDocument $approval) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $ticket = $this->approval->ticket;

        return (new MailMessage)
            ->subject('Surat Izin Penelitian Siap')
            ->greeting('Halo '.$ticket->user->name.',')
            ->line('Surat izin penelitian sudah dibuat.')
            ->line('Judul: '.$ticket->judul_penelitian)
            ->action('Lihat Tiket', route('tickets.show', $ticket->id))
            ->line('Status saat ini: menunggu_hasil (silakan unggah hasil penelitian).');
    }
}
