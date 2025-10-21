<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel belum ada → buat baru
        if (! Schema::hasTable('approval_documents')) {
            Schema::create('approval_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');

                $table->string('nama_mahasiswa');
                $table->string('nim_mahasiswa');
                $table->string('prodi_mahasiswa');

                $table->string('nama_penandatangan');
                $table->string('nip_penandatangan');
                $table->string('pangkat_gol')->nullable();
                $table->string('jabatan_penandatangan')->nullable();

                $table->string('nomor_surat');
                $table->date('tanggal_surat');
                $table->string('tujuan');
                $table->string('judul_penelitian');

                $table->string('ttd_path')->nullable();
                $table->string('stempel_path')->nullable();
                $table->string('generated_pdf_path')->nullable();

                $table->timestamps();
            });

            return;
        }

        // Jika tabel sudah ada → tambahkan kolom yang kurang
        Schema::table('approval_documents', function (Blueprint $table) {
            $columns = [
                'nama_mahasiswa'       => fn() => $table->string('nama_mahasiswa')->nullable(),
                'nim_mahasiswa'        => fn() => $table->string('nim_mahasiswa')->nullable(),
                'prodi_mahasiswa'      => fn() => $table->string('prodi_mahasiswa')->nullable(),
                'nama_penandatangan'   => fn() => $table->string('nama_penandatangan')->nullable(),
                'nip_penandatangan'    => fn() => $table->string('nip_penandatangan')->nullable(),
                'pangkat_gol'          => fn() => $table->string('pangkat_gol')->nullable(),
                'jabatan_penandatangan'=> fn() => $table->string('jabatan_penandatangan')->nullable(),
                'nomor_surat'          => fn() => $table->string('nomor_surat')->nullable(),
                'tanggal_surat'        => fn() => $table->date('tanggal_surat')->nullable(),
                'tujuan'               => fn() => $table->string('tujuan')->nullable(),
                'judul_penelitian'     => fn() => $table->string('judul_penelitian')->nullable(),
                'ttd_path'             => fn() => $table->string('ttd_path')->nullable(),
                'stempel_path'         => fn() => $table->string('stempel_path')->nullable(),
                'generated_pdf_path'   => fn() => $table->string('generated_pdf_path')->nullable(),
            ];

            foreach ($columns as $col => $adder) {
                if (! Schema::hasColumn('approval_documents', $col)) {
                    $adder();
                }
            }
        });
    }

    public function down(): void
    {
        // Tidak drop tabel biar aman
        // Kalau mau rollback, silakan uncomment:
        // Schema::dropIfExists('approval_documents');
    }
};
