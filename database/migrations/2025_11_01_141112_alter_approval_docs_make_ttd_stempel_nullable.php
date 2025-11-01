<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('approval_documents', function (Blueprint $t) {
            $t->string('ttd_path')->nullable()->change();
            $t->string('stempel_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('approval_documents', function (Blueprint $t) {
            $t->string('ttd_path')->nullable(false)->change();
            $t->string('stempel_path')->nullable(false)->change();
        });
    }
};
