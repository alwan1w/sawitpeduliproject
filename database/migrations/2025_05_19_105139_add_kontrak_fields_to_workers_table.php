<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKontrakFieldsToWorkersTable extends Migration
{
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->date('batas_kontrak')->nullable()->after('start_date');
            $table->enum('status_kontrak', ['aktif', 'putus'])
                  ->default('aktif')
                  ->after('batas_kontrak');
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['batas_kontrak', 'status_kontrak']);
        });
    }
}
