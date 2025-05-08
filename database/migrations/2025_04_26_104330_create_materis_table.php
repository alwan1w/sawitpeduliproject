<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('judul_materi');
            $table->string('file')->nullable(); // Untuk file PDF
            $table->string('tujuan', 200)->nullable();
            $table->string('deskripsi', 200)->nullable();
            $table->text('isi_materi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};
