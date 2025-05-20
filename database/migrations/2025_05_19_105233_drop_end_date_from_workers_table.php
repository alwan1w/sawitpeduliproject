<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEndDateFromWorkersTable extends Migration
{
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            // Pastikan Anda pakai nama kolom yang tepat:
            if (Schema::hasColumn('workers', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
        });
    }
}
