<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE complaint_messages MODIFY sender_type ENUM('worker','pemkab','pengawas')");
    }

    public function down()
    {
        DB::statement("ALTER TABLE complaint_messages MODIFY sender_type ENUM('worker','pemkab')");
    }

};
