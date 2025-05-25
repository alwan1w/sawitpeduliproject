<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->unsignedBigInteger('sertifikasi_id')->nullable()->after('materi_id');
            $table->foreign('sertifikasi_id')->references('id')->on('sertifikasis')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropForeign(['sertifikasi_id']);
            $table->dropColumn('sertifikasi_id');
        });
    }
};
