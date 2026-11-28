<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Penilaian wawancara minat & bakat (27 Nov 2026). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spmb_wawancara')) {
            return;
        }

        Schema::create('spmb_wawancara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('registrations')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_minat')->nullable();
            $table->unsignedTinyInteger('nilai_bakat')->nullable();
            $table->unsignedTinyInteger('nilai_komunikasi')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('penilai', 120)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_wawancara');
    }
};
