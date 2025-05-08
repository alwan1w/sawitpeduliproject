<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // hapus kolom lama
            $table->dropColumn(['cv', 'certificate', 'ijazah']);

            // tambah JSON untuk pilihan dokumen
            $table->json('required_documents')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('cv')->nullable();
            $table->string('certificate')->nullable();
            $table->string('ijazah')->nullable();

            $table->dropColumn('required_documents');
        });
    }
};
