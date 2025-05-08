<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            // HAPUS baris $table->foreignId('agency_id') karena sudah ada
            $table->enum('agency_status', ['menunggu', 'diterima', 'ditolak'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropColumn('agency_status');
        });
    }
};
