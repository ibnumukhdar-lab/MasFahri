<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BTQ (Baca Tulis Al-Qur'an) beraspek & berkriteria (27 Nov 2026):
 * 1. Baca Al-Qur'an  : tajwid, makhraj, kelancaran
 * 2. Tulis Arab      : ketepatan huruf, kerapian tulisan
 * 3. Praktek Sholat  : bacaan sholat, pemahaman fiqih, kesesuaian gerakan
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spmb_nilai_quran', function (Blueprint $table) {
            foreach ([
                'baca_tajwid', 'baca_makhraj', 'baca_kelancaran',
                'tulis_ketepatan', 'tulis_kerapian',
                'sholat_bacaan', 'sholat_fiqih', 'sholat_gerakan',
            ] as $kolom) {
                if (! Schema::hasColumn('spmb_nilai_quran', $kolom)) {
                    $table->unsignedTinyInteger($kolom)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('spmb_nilai_quran', function (Blueprint $table) {
            $table->dropColumn([
                'baca_tajwid', 'baca_makhraj', 'baca_kelancaran',
                'tulis_ketepatan', 'tulis_kerapian',
                'sholat_bacaan', 'sholat_fiqih', 'sholat_gerakan',
            ]);
        });
    }
};
