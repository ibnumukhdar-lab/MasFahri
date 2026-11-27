<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Tanggal tes per paket (hari tes), dipakai untuk judul & pengelompokan di halaman siswa. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spmb_paket_soal', function (Blueprint $table) {
            if (! Schema::hasColumn('spmb_paket_soal', 'tanggal_tes')) {
                $table->date('tanggal_tes')->nullable()->after('kkm');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spmb_paket_soal', function (Blueprint $table) {
            $table->dropColumn('tanggal_tes');
        });
    }
};
