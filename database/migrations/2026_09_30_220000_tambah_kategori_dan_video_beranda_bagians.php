<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Beranda gaya landing: tiap seksi terikat satu KATEGORI tulisan (foto utama tulisan yang tampil),
 * dan bagian hero bisa menyimpan tautan video profil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beranda_bagians', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->index();
            $table->string('video')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('beranda_bagians', function (Blueprint $table) {
            $table->dropColumn(['kategori_id', 'video']);
        });
    }
};
