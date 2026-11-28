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

    public function test_migrasi_menyediakan_dua_gelombang_contoh(): void
    {
        $this->assertSame(2, SpmbGelombang::count());
        $this->assertSame(2, SpmbJadwalTes::count());
        $this->assertSame('Gelombang 1', SpmbGelombang::orderBy('urut')->first()->nama);
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
            ->assertSee('Tambah jadwal tes');
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
}
