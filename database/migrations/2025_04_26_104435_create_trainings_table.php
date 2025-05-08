<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('tema_pelatihan');
            $table->string('moderator');
            $table->date('tanggal_pelatihan');
            $table->foreignId('materi_id')->constrained('materis')->onDelete('cascade');
            $table->integer('kuota_peserta');
            $table->string('lokasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
