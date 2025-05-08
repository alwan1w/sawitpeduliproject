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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->text('address');
            $table->string('cv')->nullable();
            $table->string('certificate')->nullable();
            $table->string('ijazah')->nullable();
            $table->enum('status', ['masuk', 'ditolak', 'seleksi'])->default('masuk');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
