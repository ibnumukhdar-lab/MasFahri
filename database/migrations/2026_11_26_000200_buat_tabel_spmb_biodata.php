<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spmb_biodata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained('registrations')->cascadeOnDelete();
            $table->string('nik', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->unsignedInteger('anak_ke')->nullable();
            $table->unsignedInteger('jumlah_saudara')->nullable();
            $table->string('nama_ayah', 120)->nullable();
            $table->string('pekerjaan_ayah', 120)->nullable();
            $table->string('nama_ibu', 120)->nullable();
            $table->string('pekerjaan_ibu', 120)->nullable();
            $table->string('telepon_rumah', 25)->nullable();
            $table->string('hobi', 150)->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spmb_biodata');
    }
};
