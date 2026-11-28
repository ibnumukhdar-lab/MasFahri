<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Akun SIAKAD dipakai untuk masuk panel /kelola:
 * - nipa      : nomor induk pegawai Arafah (ikut tampil di panel)
 * - id_siakad : id baris users di basis data SIAKAD (penaut, bukan sandi)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nipa')) {
                $table->string('nipa', 30)->nullable()->after('whatsapp');
            }

            if (! Schema::hasColumn('users', 'id_siakad')) {
                $table->unsignedBigInteger('id_siakad')->nullable()->after('nipa');
                $table->index('id_siakad');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_siakad')) {
                $table->dropIndex(['id_siakad']);
                $table->dropColumn('id_siakad');
            }

            if (Schema::hasColumn('users', 'nipa')) {
                $table->dropColumn('nipa');
            }
        });
    }
};
