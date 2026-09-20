<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bagian-bagian halaman beranda yang bisa disunting dari panel.
 * Satu baris = satu bagian beranda; urutan menentukan tampilnya (bisa digeser).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beranda_bagians', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 40);              // hero, tentang, keunggulan, ...
            $table->integer('urutan')->default(0);    // urutan tampil (bisa di-drag)
            $table->boolean('aktif')->default(true);  // sembunyikan tanpa dihapus
            $table->string('judul')->nullable();
            $table->string('subjudul')->nullable();
            $table->text('teks')->nullable();
            $table->string('gambar')->nullable();     // gambar utama bagian
            $table->string('tombol_teks')->nullable();
            $table->string('tombol_tautan')->nullable();
            $table->json('data')->nullable();         // daftar butir/kartu/tanya-jawab
            $table->timestamps();

            $table->index(['aktif', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beranda_bagians');
    }
};
