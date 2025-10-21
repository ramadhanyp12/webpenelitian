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
    Schema::create('approval_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();

        // data surat
        $table->string('nama_mahasiswa');
        $table->string('nim_mahasiswa', 50);
        $table->string('prodi_mahasiswa');
        $table->string('nama_penandatangan');
        $table->string('nip_penandatangan')->nullable();
        $table->string('pangkat_gol')->nullable();
        $table->string('jabatan_penandatangan')->nullable();
        $table->string('nomor_surat')->unique();
        $table->date('tanggal_surat');
        $table->string('tujuan');             // "Kepada Yth ..."
        $table->string('judul_penelitian');

        // file tanda tangan & stempel (terpisah sesuai revisimu)
        $table->string('ttd_path');           // wajib saat approve
        $table->string('stempel_path');       // wajib saat approve

        // hasil generate (jaga2 jika simpan versi final di sini juga)
        $table->string('generated_pdf_path')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('approval_documents');
}
};
