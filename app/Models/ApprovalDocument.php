<?php

namespace App\Models;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ApprovalDocument extends Model
{
    protected $table = 'approval_documents';

    protected $fillable = [
        'ticket_id','nama_mahasiswa','nim_mahasiswa','prodi_mahasiswa','nama_penandatangan',
        'nip_penandatangan','pangkat_gol','jabatan_penandatangan','nomor_surat','tanggal_surat',
        'tujuan','judul_penelitian','ttd_path','stempel_path',
        'generated_pdf_path','signed_pdf_path','released_to_user',
    ];

    // URL helper
    public function getGeneratedPdfUrlAttribute()
    {
        return $this->generated_pdf_path ? Storage::url($this->generated_pdf_path) : null;
    }

    public function getSignedPdfUrlAttribute()
    {
        return $this->signed_pdf_path ? Storage::url($this->signed_pdf_path) : null;
    }
    
    // >>> INI YANG WAJIB ADA <<<
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
