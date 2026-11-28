<?php

namespace Tests\Feature;

use App\Filament\Resources\SpmbGelombangs\Pages\EditSpmbGelombang;
use App\Filament\Resources\SpmbGelombangs\RelationManagers\JadwalTesRelationManager;
use App\Models\Registration;
use App\Models\SpmbGelombang;
use App\Models\SpmbJadwalTes;
use App\Models\SpmbPaketSoal;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Jadwal SPMB sebagai data: gelombang, jadwal tes, tautan paket CBT,
 * dan pendaftar otomatis masuk gelombang berjalan.
 */
class GelombangSpmbTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'admin@uji.test',
            'password' => 'rahasia123',
            'peran' => 'super_admin',
            'aktif' => true,
        ]);
    }

    public function test_migrasi_menyediakan_dua_gelombang_dengan_tiga_tahapan(): void
    {
        $this->assertSame(2, SpmbGelombang::count());
        $this->assertSame(6, SpmbJadwalTes::count(), 'tiap hari tes berisi TPA + BTQ + wawancara');

        $gel = SpmbGelombang::orderBy('urut')->first();
        $this->assertSame('Gelombang 1', $gel->nama);

        // Satu hari tes = satu tanggal berisi tiga tahapan.
        $perHari = $gel->jadwalPerHari();
        $this->assertCount(1, $perHari, 'gelombang contoh hanya punya satu hari tes');
        $this->assertCount(3, $perHari->first());
        $this->assertSame(
            ['akademik', 'btq', 'wawancara'],
            $perHari->first()->pluck('jenis')->all()
        );
    }

    public function test_halaman_spmb_menampilkan_jadwal_dari_data(): void
    {
        $gel = SpmbGelombang::orderBy('urut')->first();
        $gel->update(['mulai' => now()->subDays(2), 'selesai' => now()->addMonths(1)]);
        SpmbJadwalTes::where('gelombang_id', $gel->id)->update(['tanggal' => now()->addDays(5)]);

        $res = $this->get('/spmb');
        $res->assertOk();
        $res->assertSee('Jadwal SPMB');
        $res->assertSee($gel->nama);
        $res->assertSee('Tes Potensi Akademik');
        $res->assertSee('Baca Tulis Al-Qur\'an');
        $res->assertSee('Wawancara');
        $res->assertSee('Telah Dibuka', false);
    }

    public function test_panel_gelombang_dan_relation_manager_jadwal_tampil(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $gel = SpmbGelombang::orderBy('urut')->first();
        $jadwal = $gel->jadwalTes()->first();

        Livewire::test(JadwalTesRelationManager::class, [
            'ownerRecord' => $gel,
            'pageClass' => EditSpmbGelombang::class,
        ])
            ->assertOk()
            ->assertSee($jadwal->nama)
            ->assertSee('Tambah satu tahapan')
            ->assertSee('Tambah hari tes lengkap');
    }

    public function test_relation_manager_bisa_menambah_jadwal_tes(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $gel = SpmbGelombang::orderBy('urut')->first();
        $sebelum = $gel->jadwalTes()->count();

        Livewire::test(JadwalTesRelationManager::class, [
            'ownerRecord' => $gel,
            'pageClass' => EditSpmbGelombang::class,
        ])
            ->callTableAction('create', data: [
                'jenis' => 'wawancara',
                'nama' => 'Wawancara Uji Panel',
                'tanggal' => now()->addDays(9)->toDateString(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'tempat' => 'Ruang BK',
                'urut' => 2,
                'aktif' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertSame($sebelum + 1, $gel->jadwalTes()->count());
        $this->assertDatabaseHas('spmb_jadwal_tes', ['nama' => 'Wawancara Uji Panel', 'gelombang_id' => $gel->id]);
    }

    public function test_paket_cbt_yang_ditautkan_mengikuti_jadwal(): void
    {
        $jadwal = SpmbJadwalTes::orderBy('tanggal')->first();

        $paket = SpmbPaketSoal::create([
            'judul' => 'Paket Tautan',
            'durasi_menit' => 30,
            'wajib' => true,
            'aktif' => true,
            'jadwal_tes_id' => $jadwal->id,
        ]);

        $this->assertSame($jadwal->tanggal->toDateString(), $paket->fresh()->tanggal_tes->toDateString());
    }

    public function test_pendaftar_otomatis_masuk_gelombang_berjalan(): void
    {
        $gel = SpmbGelombang::orderBy('urut')->first();
        $gel->update(['mulai' => now()->subDay(), 'selesai' => now()->addMonth()]);

        \Illuminate\Support\Facades\Cache::flush();

        $res = $this->post('/spmb', [
            'nama_wali' => 'Wali Uji',
            'nama_siswa' => 'Siswa Uji',
            'tahun_ajaran' => '2027/2028',
            'whatsapp' => '081200000001',
            'jenis_kelamin' => 'Laki-laki',
            'minat_program' => 'Reguler',
        ]);

        $res->assertRedirect();
        $daftar = Registration::where('nama_siswa', 'Siswa Uji')->first();
        $this->assertNotNull($daftar, 'pendaftaran tidak tersimpan');
        $this->assertSame($gel->id, $daftar->gelombang_id);
    }

    public function test_aksi_tambah_hari_tes_lengkap_membuat_tiga_tahapan(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $gel = SpmbGelombang::orderBy('urut')->first();
        $sebelum = SpmbJadwalTes::count();
        $tanggal = now()->addDays(20)->toDateString();

        Livewire::test(JadwalTesRelationManager::class, [
            'ownerRecord' => $gel,
            'pageClass' => EditSpmbGelombang::class,
        ])
            ->callTableAction('tambahHariLengkap', data: [
                'tanggal' => $tanggal,
                'tempat' => 'Kampus SMA IT Arafah',
                'tpa_mulai' => '08:00', 'tpa_selesai' => '10:00',
                'btq_mulai' => '10:30', 'btq_selesai' => '11:30',
                'waw_mulai' => '13:00', 'waw_selesai' => '15:00',
                'aktif' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertSame($sebelum + 3, SpmbJadwalTes::count());
        $this->assertSame(
            ['akademik', 'btq', 'wawancara'],
            SpmbJadwalTes::whereDate('tanggal', $tanggal)->orderBy('urut')->pluck('jenis')->all()
        );
    }

    public function test_jadwal_dikelompokkan_per_hari(): void
    {
        $gel = SpmbGelombang::orderBy('urut')->first();

        $hari2 = now()->addDays(30)->toDateString();
        foreach ([['akademik', '08:00', '10:00'], ['btq', '10:30', '11:30'], ['wawancara', '13:00', '15:00']] as $i => [$jenis, $mulai, $selesai]) {
            $gel->jadwalTes()->create([
                'jenis' => $jenis, 'nama' => 'Tahap '.$jenis, 'tanggal' => $hari2,
                'jam_mulai' => $mulai, 'jam_selesai' => $selesai, 'urut' => $i + 1, 'aktif' => true,
            ]);
        }

        $perHari = $gel->jadwalPerHari();
        $this->assertCount(2, $perHari, 'dua hari tes');
        $this->assertCount(3, $perHari->last(), 'hari kedua berisi tiga tahapan');
    }
}
