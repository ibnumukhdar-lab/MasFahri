<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CBT multi-paket (27 Nov 2026): satu hari tes bisa terdiri dari beberapa paket
 * (per mata pelajaran). Paket punya urutan, penanda wajib, dan KKM sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spmb_paket_soal', function (Blueprint $table) {
            if (! Schema::hasColumn('spmb_paket_soal', 'mata_pelajaran')) {
                $table->string('mata_pelajaran', 80)->nullable()->after('judul');
            }
            if (! Schema::hasColumn('spmb_paket_soal', 'urutan')) {
                $table->integer('urutan')->default(0)->after('durasi_menit');
            }
            if (! Schema::hasColumn('spmb_paket_soal', 'wajib')) {
                $table->boolean('wajib')->default(true)->after('urutan');
            }
            if (! Schema::hasColumn('spmb_paket_soal', 'kkm')) {
                $table->integer('kkm')->nullable()->after('wajib');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spmb_paket_soal', function (Blueprint $table) {
            $table->dropColumn(['mata_pelajaran', 'urutan', 'wajib', 'kkm']);
        });
    }
};
