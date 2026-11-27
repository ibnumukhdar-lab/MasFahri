<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel formulir pendaftaran murid baru — field mengikuti formulir resmi
 * (Data Pribadi + Data Orang Tua/Wali) + pas foto latar merah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_formulir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('registrations')->cascadeOnDelete();

            // I. DATA PRIBADI
            $table->string('nama_panggilan', 100)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('golongan_darah', 3)->nullable();
            $table->string('suku_bangsa', 80)->nullable();
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('dari_bersaudara')->nullable();
            $table->string('alamat_jalan', 255)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();

            // II. DATA ORANG TUA / WALI MURID
            $table->string('ayah_nama', 120)->nullable();
            $table->string('ayah_ttl', 150)->nullable();
            $table->string('ayah_pendidikan', 60)->nullable();
            $table->string('ayah_pekerjaan', 120)->nullable();
            $table->string('ayah_penghasilan', 20)->nullable();
            $table->string('ayah_telepon', 25)->nullable();

            $table->string('ibu_nama', 120)->nullable();
            $table->string('ibu_ttl', 150)->nullable();
            $table->string('ibu_pendidikan', 60)->nullable();
            $table->string('ibu_pekerjaan', 120)->nullable();
            $table->string('ibu_penghasilan', 20)->nullable();
            $table->string('ibu_telepon', 25)->nullable();

            $table->string('foto_latar_merah', 255)->nullable();
            $table->timestamp('disimpan_pada')->nullable();
            $table->timestamp('terkirim_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_formulir');
    }
};
