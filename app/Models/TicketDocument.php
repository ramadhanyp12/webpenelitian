<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'type',          // 'surat' atau 'lampiran'
        'file_path',     // path file di storage
        'original_name', // nama asli file
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Hapus file fisik otomatis saat dokumen dihapus
     */
    protected static function booted(): void
    {
        static::deleting(function (TicketDocument $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        });
    }
}
