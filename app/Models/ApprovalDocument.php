<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalDocument extends Model
{
    protected $fillable = [
        'ticket_id',
        'nama_mahasiswa',
        'nim_mahasiswa',
        'prodi_mahasiswa',
        'nama_penandatangan',
        'nip_penandatangan',
        'pangkat_gol',
        'jabatan_penandatangan',
        'nomor_surat',
        'tanggal_surat',
        'tujuan',
        'judul_penelitian',
        'ttd_path',
        'stempel_path',
        'generated_pdf_path',
        'signed_pdf_path',
        'released_to_user',
    ];

    protected $casts = [
        'released_to_user' => 'boolean',
        'tanggal_surat' => 'date',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

}
