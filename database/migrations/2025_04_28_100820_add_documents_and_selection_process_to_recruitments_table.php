<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->json('required_documents')->nullable()->after('agency_status');
            $table->text('selection_process')->nullable()->after('required_documents');
        });
    }

    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropColumn(['required_documents', 'selection_process']);
        });
    }
};
