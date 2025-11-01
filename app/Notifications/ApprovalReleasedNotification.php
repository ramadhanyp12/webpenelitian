namespace App\Notifications;

use App\Models\ApprovalDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApprovalReleasedNotification extends Notification
{
    use Queueable;

    public function __construct(public ApprovalDocument $approval) {}

    public function via($notifiable): array
    {
        return ['mail', 'database']; // sesuaikan channel yang kamu pakai
    }

    public function toMail($notifiable): MailMessage
    {
        $ticket = $this->approval->ticket;
        return (new MailMessage)
            ->subject('Surat Rekomendasi Siap Diunduh')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Surat rekomendasi penelitian kamu sudah disetujui dan siap diunduh.')
            ->action('Lihat Tiket', route('tickets.show', $ticket->id))
            ->line('Status tiket sekarang: MENUNGGU_HASIL.');
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id'      => $this->approval->ticket_id,
            'message'        => 'Surat rekomendasi siap diunduh. Status: MENUNGGU_HASIL.',
        ];
    }
}
