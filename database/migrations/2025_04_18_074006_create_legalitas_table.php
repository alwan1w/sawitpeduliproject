<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legalitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('users')->onDelete('cascade');

            // Data perusahaan
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->string('email')->nullable();

            // Nomor dokumen
            $table->string('akta')->nullable();
            $table->string('nib')->nullable();
            $table->string('suip')->nullable();
            $table->string('tdp')->nullable();
            $table->string('npwp')->nullable();
            $table->string('izin_operasional')->nullable();

            // Dokumen upload
            $table->string('file_akta')->nullable();
            $table->string('file_nib')->nullable();
            $table->string('file_suip')->nullable();
            $table->string('file_tdp')->nullable();
            $table->string('file_npwp')->nullable();
            $table->string('file_izin_operasional')->nullable();

            $table->enum('status', ['draft', 'menunggu_verifikasi', 'terverifikasi', 'ditolak'])->default('draft');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalitas');
    }
};
