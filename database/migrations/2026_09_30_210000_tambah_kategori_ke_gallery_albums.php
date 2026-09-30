<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kategori album galeri — dipakai beranda gaya landing page:
 * prestasi · pembelajaran · fasilitas-sekolah · fasilitas-asrama · student-root · diniyah · alumni
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_albums', function (Blueprint $table) {
            $table->string('kategori')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('gallery_albums', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
