<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->integer('contract_duration')->unsigned()->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->integer('contract_duration')->default(0)->change();
        });
    }

};
