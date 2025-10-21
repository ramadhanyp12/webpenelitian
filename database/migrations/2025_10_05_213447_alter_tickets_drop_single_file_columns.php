<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Hapus kolom lama jika masih ada
            if (Schema::hasColumn('tickets', 'surat_pdf_path')) {
                $table->dropColumn('surat_pdf_path');
            }
            if (Schema::hasColumn('tickets', 'lampiran_pdf_path')) {
                $table->dropColumn('lampiran_pdf_path');
            }
        });
    }

    public function down(): void
    {
        // Kembalikan kolom (nullable supaya aman kalau rollback)
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'surat_pdf_path')) {
                $table->string('surat_pdf_path')->nullable();
            }
            if (!Schema::hasColumn('tickets', 'lampiran_pdf_path')) {
                $table->string('lampiran_pdf_path')->nullable();
            }
        });
    }
};
