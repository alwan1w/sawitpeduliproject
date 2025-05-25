<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSertifikasisTable extends Migration
{
    public function up()
    {
        Schema::create('sertifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sertifikasi');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sertifikasis');
    }
};
