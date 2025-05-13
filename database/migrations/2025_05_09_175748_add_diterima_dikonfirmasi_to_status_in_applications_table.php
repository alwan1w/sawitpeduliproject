<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE applications MODIFY status ENUM('masuk', 'ditolak', 'seleksi', 'diterima', 'dikonfirmasi') DEFAULT 'masuk'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE applications MODIFY status ENUM('masuk', 'ditolak', 'seleksi') DEFAULT 'masuk'");
    }
};
