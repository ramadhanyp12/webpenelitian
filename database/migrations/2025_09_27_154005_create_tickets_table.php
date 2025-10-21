<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        // data isian tiket (sebagian duplikasi dr profil agar historis tidak berubah)
        $table->string('nama');
        $table->string('nim', 50);
        $table->string('program_studi');
        $table->string('kampus');
        $table->string('tahun_ajaran', 20);
        $table->string('judul_penelitian');

        // file
        $table->string('surat_pdf_path');          // wajib (upload dari user)
        $table->string('lampiran_pdf_path')->nullable(); // opsional

        // metadata
        $table->text('keterangan');                    // keterangan dari user saat submit
        $table->string('lokasi_pengadilan');          // tujuan pengadilan

        // status alur
        $table->enum('status', [
            'dikirim',               // baru dikirim user
            'menunggu_persetujuan',  // admin set menunggu
            'disetujui',             // admin setujui
            'ditolak',               // admin tolak
            'selesai'                // proses complete
        ])->default('dikirim');

        // catatan & hubungan proses
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('approved_at')->nullable();
        $table->text('alasan_ditolak')->nullable();

        // hasil (misal surat izin yang di-generate & diunggah admin)
        $table->string('hasil_pdf_path')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('tickets');
}
};
