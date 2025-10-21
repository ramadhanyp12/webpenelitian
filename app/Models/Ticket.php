<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama', 'nim', 'program_studi', 'kampus', 'tahun_ajaran',
        'judul_penelitian', 'keterangan', 'lokasi_pengadilan', 'status',
        'approved_by', 'approved_at', 'alasan_ditolak', 'hasil_pdf_path', // tambahkan ini kalau kolomnya ada
        // ⚠️ TIDAK ada: surat_pdf_path, lampiran_pdf_path (karena sudah pindah ke tabel ticket_documents)
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Pemilik ticket
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // User yang meng-approve (jika ada)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Dokumen hasil/approval tunggal (kalau kamu pakai)
    public function approvalDocument()
    {
        return $this->hasOne(ApprovalDocument::class);
    }

    // ===== Relasi multi-file dokumen =====
    public function documents(): HasMany
    {
        return $this->hasMany(TicketDocument::class);
    }

    public function suratDocuments(): HasMany
    {
        return $this->documents()->where('type', 'surat');
    }

    public function lampiranDocuments(): HasMany
    {
        return $this->documents()->where('type', 'lampiran');
    }
}
