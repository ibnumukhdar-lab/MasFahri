<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Bank soal lintas paket (27 Nov 2026). */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('spmb_bank_soal')) {
            return;
        }

        Schema::create('spmb_bank_soal', function (Blueprint $table) {
            $table->id();
            $table->string('mata_pelajaran', 80)->nullable();
            $table->text('teks');
            $table->string('opsi_a', 255);
            $table->string('opsi_b', 255);
            $table->string('opsi_c', 255)->nullable();
            $table->string('opsi_d', 255)->nullable();
            $table->string('opsi_e', 255)->nullable();
            $table->char('kunci', 1)->default('A');
            $table->string('tingkat', 20)->default('sedang'); // mudah | sedang | sulit
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['mata_pelajaran', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_bank_soal');
    }
};
