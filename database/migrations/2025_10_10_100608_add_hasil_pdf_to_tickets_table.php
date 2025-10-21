<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // tambahkan kolom hasil (pdf tunggal, opsional)
            if (!Schema::hasColumn('tickets', 'hasil_pdf_path')) {
                $table->string('hasil_pdf_path')->nullable()->after('alasan_ditolak');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'hasil_pdf_path')) {
                $table->dropColumn('hasil_pdf_path');
            }
        });
    }
};
