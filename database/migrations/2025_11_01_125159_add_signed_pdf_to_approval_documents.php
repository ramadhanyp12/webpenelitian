<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('approval_documents', function (Blueprint $t) {
            $t->string('signed_pdf_path')->nullable()->after('generated_pdf_path');
            $t->boolean('released_to_user')->default(false)->after('signed_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('approval_documents', function (Blueprint $t) {
            $t->dropColumn(['signed_pdf_path', 'released_to_user']);
        });
    }
};
