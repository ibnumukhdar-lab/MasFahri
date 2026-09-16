<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema lengkap situs smaitarafah.sch.id (Laravel).
 * Semua tabel dibuat dalam satu migrasi karena ini proyek baru (greenfield).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------- pengguna: peran & profil alumni ----------
        Schema::table('users', function (Blueprint $table) {
            $table->string('peran')->default('alumni')->after('password'); // admin|guru|musyrif|ustadz_diniyah|orang_tua|alumni
            $table->string('whatsapp')->nullable()->after('peran');
            $table->boolean('aktif')->default(true)->after('whatsapp');
        });

        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tahun_lulus', 8)->nullable()->index();
            $table->string('kelas_akhir')->nullable();
            $table->string('lanjut_ke')->nullable();       // kuliah / kerja / pesantren
            $table->string('nama_kampus')->nullable();
            $table->string('kota')->nullable();
            $table->string('instagram')->nullable();
            $table->text('pesan')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('tampil_publik')->default(false);
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });

        // ---------- konten ----------
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('deskripsi')->nullable();
            $table->string('warna', 20)->default('slate');
            $table->unsignedInteger('wp_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('body')->nullable();
            $table->string('status', 12)->default('draf');   // draf|terbit
            $table->timestamp('terbit_pada')->nullable()->index();
            $table->string('gambar_sampul')->nullable();     // path lokal atau URL lama
            $table->foreignId('penulis_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('wp_id')->nullable()->unique();
            $table->unsignedInteger('dilihat')->default(0);
            $table->timestamps();
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'category_id']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->longText('body')->nullable();            // HTML hasil konversi
            $table->json('blocks')->nullable();              // blok terstruktur (cadangan)
            $table->string('status', 12)->default('terbit');
            $table->unsignedInteger('urut')->default(0);
            $table->boolean('tampil_di_menu')->default(false);
            $table->string('meta_judul')->nullable();
            $table->string('meta_deskripsi', 300)->nullable();
            $table->unsignedInteger('wp_id')->nullable()->unique();
            $table->timestamps();
        });

        // ---------- galeri ----------
        Schema::create('gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('sampul')->nullable();
            $table->boolean('terbit')->default(true);
            $table->unsignedInteger('urut')->default(0);
            $table->timestamps();
        });

        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_album_id')->constrained()->cascadeOnDelete();
            $table->string('berkas');       // path relatif disk media
            $table->string('judul')->nullable();
            $table->unsignedInteger('urut')->default(0);
            $table->timestamps();
        });

        // ---------- SPMB ----------
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();          // SPMB-2026-0001
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->string('nama_wali');
            $table->string('nama_siswa');
            $table->string('jenis_kelamin', 12)->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->string('minat_program')->nullable();     // reguler boarding / jalur prestasi
            $table->string('whatsapp', 25)->nullable();
            $table->string('email')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('baru');   // baru|dihubungi|tes|diterima|ditolak
            $table->string('ip', 45)->nullable();
            $table->text('pesan_admin')->nullable();
            $table->timestamps();
        });

        // ---------- kelulusan ----------
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->index();
            $table->string('nama');
            $table->string('kelas')->nullable();
            $table->string('tahun_ajaran', 20)->default('2025/2026');
            $table->string('status', 20)->default('lulus');  // lulus|tidak_lulus
            $table->text('pesan')->nullable();
            $table->boolean('tampil')->default(true);
            $table->timestamps();
            $table->unique(['nisn', 'tahun_ajaran']);
        });

        // ---------- tautan guru & pengaturan ----------
        Schema::create('teacher_links', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('url', 500);
            $table->string('kelompok')->default('Umum');   // Umum|AI Prompt|Perangkat Guru|Video
            $table->string('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urut')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('teacher_links');
        Schema::dropIfExists('graduates');
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('gallery_photos');
        Schema::dropIfExists('gallery_albums');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('alumni_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peran', 'whatsapp', 'aktif']);
        });
    }
};
