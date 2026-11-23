<?php

/**
 * Data Alumni — isi via formulir link yang dibagikan ke komunitas.
 * Tanpa akun: nama + WA + foto + cerita. Admin verifikasi lalu tampil publik.
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_cerita', function (Blueprint $t) {
            $t->id();
            $t->string('nama', 120);
            $t->string('wa', 25);
            $t->string('tahun_lulus', 8)->nullable();
            $t->string('kelas_akhir', 40)->nullable();
            $t->string('status_saat_ini', 20)->nullable(); // kuliah|bekerja|wirausaha|tahfizh|lainnya
            $t->string('status_keterangan', 160)->nullable(); // kampus/jabatan/kota
            $t->text('prestasi')->nullable();
            $t->text('pesan')->nullable();
            $t->string('instagram', 80)->nullable();
            $t->string('foto', 255)->nullable();
            $t->boolean('tampil')->default(false);
            $t->string('token', 40)->nullable()->unique(); // tautan sunting ulang
            $t->datetime('diverifikasi_pada')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_cerita');
    }
};
