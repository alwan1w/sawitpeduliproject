<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('complaint_messages', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->string('image')->nullable()->after('message');
        });
    }

    public function down()
    {
        Schema::table('complaint_messages', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('image');
        });
    }

};
