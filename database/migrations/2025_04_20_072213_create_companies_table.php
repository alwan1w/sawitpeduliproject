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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Perusahaan
            $table->string('director')->nullable(); // Direktur
            $table->string('phone')->nullable(); // Kontak
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            $table->string('nib')->nullable();
            $table->string('tdp')->nullable();
            $table->string('akta')->nullable();
            $table->string('suip')->nullable();
            $table->string('npwp')->nullable();
            $table->string('izin_operasional')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
