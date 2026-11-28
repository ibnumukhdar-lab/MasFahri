<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Jadwal SPMB jadi data (bukan tulisan di halaman):
 * - spmb_gelombang   : satu baris = satu gelombang per tahun ajaran
 * - spmb_jadwal_tes  : jadwal tes masuk, banyak per gelombang
 * - registrations.gelombang_id  : pendaftar otomatis masuk gelombang berjalan
 * - spmb_paket_soal.jadwal_tes_id : paket CBT boleh mengikuti tanggal jadwal
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('spmb_gelombang')) {
            Schema::create('spmb_gelombang', function (Blueprint $table) {
                $table->id();
                $table->string('tahun_ajaran', 20);
                $table->string('nama', 60);              // Gelombang 1
                $table->unsignedSmallInteger('urut')->default(1);
                $table->date('mulai')->nullable();       // awal pendaftaran
                $table->date('selesai')->nullable();     // akhir pendaftaran
                $table->text('keterangan')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();

                $table->unique(['tahun_ajaran', 'nama']);
                $table->index(['tahun_ajaran', 'aktif']);
            });
        }

        if (! Schema::hasTable('spmb_jadwal_tes')) {
            Schema::create('spmb_jadwal_tes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gelombang_id')->constrained('spmb_gelombang')->cascadeOnDelete();
                $table->string('jenis', 20)->default('akademik'); // akademik|btq|wawancara|lain
                $table->string('nama', 120);                      // Tes Potensi Akademik
                $table->date('tanggal');
                $table->time('jam_mulai')->nullable();
                $table->time('jam_selesai')->nullable();
                $table->string('tempat', 120)->nullable();
                $table->text('keterangan')->nullable();
                $table->unsignedSmallInteger('urut')->default(1);
                $table->boolean('aktif')->default(true);
                $table->timestamps();

                $table->index(['gelombang_id', 'tanggal']);
            });
        }

        if (! Schema::hasColumn('registrations', 'gelombang_id')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->unsignedBigInteger('gelombang_id')->nullable()->after('tahun_ajaran');
                $table->index('gelombang_id');
            });
        }

        if (! Schema::hasColumn('spmb_paket_soal', 'jadwal_tes_id')) {
            Schema::table('spmb_paket_soal', function (Blueprint $table) {
                $table->unsignedBigInteger('jadwal_tes_id')->nullable()->after('tanggal_tes');
                $table->index('jadwal_tes_id');
            });
        }

        // Isi awal = jadwal yang sekarang tertulis di halaman /spmb, supaya
        // situs tidak kosong dan panitia cukup menyunting.
        if (DB::table('spmb_gelombang')->count() === 0) {
            $gel1 = DB::table('spmb_gelombang')->insertGetId([
                'tahun_ajaran' => '2027/2028',
                'nama' => 'Gelombang 1',
                'urut' => 1,
                'mulai' => '2026-12-01',
                'selesai' => '2027-02-28',
                'keterangan' => 'Setelah tanggal tes pertama, tes berikutnya dijadwalkan berkelanjutan sampai kuota gelombang terisi — ikuti arahan panitia lewat WhatsApp.',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $gel2 = DB::table('spmb_gelombang')->insertGetId([
                'tahun_ajaran' => '2027/2028',
                'nama' => 'Gelombang 2',
                'urut' => 2,
                'mulai' => '2027-03-01',
                'selesai' => '2027-06-30',
                'keterangan' => 'Kursi mengikuti sisa kuota, jadi daftar lebih awal tetap lebih menguntungkan.',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('spmb_jadwal_tes')->insert([
                [
                    'gelombang_id' => $gel1,
                    'jenis' => 'akademik',
                    'nama' => 'Tes Potensi Akademik & Minat Bakat',
                    'tanggal' => '2026-12-14',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '10:00',
                    'tempat' => 'Kampus SMA IT Arafah',
                    'keterangan' => 'Dikerjakan daring (CBT) di perangkat masing-masing.',
                    'urut' => 1,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'gelombang_id' => $gel2,
                    'jenis' => 'akademik',
                    'nama' => 'Tes Potensi Akademik & Minat Bakat',
                    'tanggal' => '2027-03-15',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '10:00',
                    'tempat' => 'Kampus SMA IT Arafah',
                    'keterangan' => null,
                    'urut' => 1,
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('spmb_paket_soal', 'jadwal_tes_id')) {
            Schema::table('spmb_paket_soal', function (Blueprint $table) {
                $table->dropIndex(['jadwal_tes_id']);
                $table->dropColumn('jadwal_tes_id');
            });
        }

        if (Schema::hasColumn('registrations', 'gelombang_id')) {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropIndex(['gelombang_id']);
                $table->dropColumn('gelombang_id');
            });
        }

        Schema::dropIfExists('spmb_jadwal_tes');
        Schema::dropIfExists('spmb_gelombang');
    }
};
