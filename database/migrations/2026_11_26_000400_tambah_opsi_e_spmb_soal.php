<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pilihan ganda CBT SPMB diperluas sampai E (sebelumnya A–D). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spmb_soal', function (Blueprint $table) {
            $table->string('opsi_e')->nullable()->after('opsi_d');
        });
    }

    public function down(): void
    {
        Schema::table('spmb_soal', function (Blueprint $table) {
            $table->dropColumn('opsi_e');
        });
    }
};
