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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();

            // relasi ke applications
            $table->foreignId('application_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // relasi ke recruitments
            $table->foreignId('recruitment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // relasi ke companies
            $table->foreignId('company_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // relasi ke users (pekerja)
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // tanggal mulai bekerja (boleh null)
            $table->date('start_date')->nullable();

            $table->timestamps();

            // bila diperlukan, index unik agar tidak ada duplikat
            $table->unique(['application_id', 'user_id'], 'workers_app_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropUnique('workers_app_user_unique');
        });
        Schema::dropIfExists('workers');
    }
};
