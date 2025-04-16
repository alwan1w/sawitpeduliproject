<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('users');
            $table->foreignId('agency_id')->nullable()->constrained('users');
            $table->string('position');
            $table->text('detail_posisi')->nullable();
            $table->integer('requirement_total');
            $table->date('open_date');
            $table->date('close_date');
            $table->string('salary_range')->nullable();
            $table->string('contract_duration')->nullable();
            $table->text('skills')->nullable();
            $table->string('age_range')->nullable();
            $table->string('education')->nullable();
            $table->enum('status', ['mencari_agen', 'mencari_pekerja', 'selesai'])->default('mencari_agen');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
