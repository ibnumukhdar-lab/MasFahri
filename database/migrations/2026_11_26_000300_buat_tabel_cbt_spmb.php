<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CBT tes masuk SPMB:
 * - kode_tes di registrations (tercetak di kartu tes, gerbang masuk CBT)
 * - spmb_paket_soal  : paket ujian (durasi, jadwal buka-tutup, aktif)
 * - spmb_soal        : butir soal pilihan ganda (A-D) per paket
 * - spmb_ujian       : percobaan siswa (1x per paket), skor + pelanggaran
 * - spmb_nilai_quran : KERANGKA nilai baca tulis Al-Qur'an (input panitia, menyusul)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('kode_tes', 10)->nullable()->unique()->after('nomor');
        });

        Schema::create('spmb_paket_soal', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->default(30);
            $table->dateTime('mulai_pada')->nullable();
            $table->dateTime('selesai_pada')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('spmb_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('spmb_paket_soal')->cascadeOnDelete();
            $table->unsignedSmallInteger('urut')->default(0);
            $table->text('teks');
            $table->string('opsi_a');
            $table->string('opsi_b');
            $table->string('opsi_c')->nullable();
            $table->string('opsi_d')->nullable();
            $table->string('kunci', 1)->default('A');
            $table->timestamps();
        });

        Schema::create('spmb_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('paket_id')->constrained('spmb_paket_soal')->cascadeOnDelete();
            $table->string('status')->default('sedang'); // sedang | selesai
            $table->integer('skor')->nullable();
            $table->unsignedSmallInteger('benar')->nullable();
            $table->unsignedSmallInteger('total')->nullable();
            $table->unsignedSmallInteger('pelanggaran')->default(0);
            $table->text('jawaban')->nullable(); // JSON: {soal_id: "A"}
            $table->dateTime('mulai_pada');
            $table->dateTime('selesai_pada')->nullable();
            $table->timestamps();

            $table->unique(['registration_id', 'paket_id']);
        });

        Schema::create('spmb_nilai_quran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('registrations')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_baca')->nullable();
            $table->unsignedTinyInteger('nilai_tulis')->nullable();
            $table->text('catatan')->nullable();
            $table->string('penilai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_nilai_quran');
        Schema::dropIfExists('spmb_ujian');
        Schema::dropIfExists('spmb_soal');
        Schema::dropIfExists('spmb_paket_soal');

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('kode_tes');
        });
    }
};
