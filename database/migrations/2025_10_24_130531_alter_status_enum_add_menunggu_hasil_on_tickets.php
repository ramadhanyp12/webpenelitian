<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `status` ENUM(
                'dikirim',
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'menunggu_hasil',
                'selesai'
            ) NOT NULL DEFAULT 'dikirim'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `tickets`
            MODIFY `status` ENUM(
                'dikirim',
                'menunggu_persetujuan',
                'disetujui',
                'ditolak',
                'selesai'
            ) NOT NULL DEFAULT 'dikirim'
        ");
    }
};
