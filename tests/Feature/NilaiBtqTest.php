<?php

namespace Tests\Feature;

use App\Filament\Resources\SpmbNilaiQurans\Pages\EditSpmbNilaiQuran;
use App\Filament\Resources\SpmbNilaiQurans\Pages\ListSpmbNilaiQurans;
use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use App\Models\Registration;
use App\Models\SpmbNilaiQuran;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Alur penilaian BTQ untuk guru penguji:
 * tombol "Input nilai" → nomor pendaftaran → langsung halaman penilaian siswa.
 */
class NilaiBtqTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Guru Penguji',
            'email' => 'penguji@uji.test',
            'password' => 'rahasia123',
            'peran' => 'panitia_spmb',
            'aktif' => true,
        ]);
    }

    private function pendaftar(string $nomor = 'SPMB-2027-0001'): Registration
    {
        return Registration::create([
            'nomor' => $nomor,
            'tahun_ajaran' => '2027/2028',
            'nama_wali' => 'Wali Uji',
            'nama_siswa' => 'Siswa Uji',
            'jenis_kelamin' => 'Laki-laki',
            'status' => 'tes',
        ]);
    }

    public function test_nomor_pendaftaran_diterima_dalam_beberapa_bentuk(): void
    {
        $this->pendaftar('SPMB-2027-0007');

        foreach (['SPMB-2027-0007', 'spmb-2027-7', '2027-0007', ' 2027-7 ', '20270007'] as $bentuk) {
            $this->assertNotNull(Registration::cariNomor($bentuk), "gagal untuk: {$bentuk}");
        }

        $this->assertNull(Registration::cariNomor('SPMB-2027-9999'));
        $this->assertNull(Registration::cariNomor(''));
    }

    public function test_tombol_input_nilai_membuat_baris_dan_membuka_halaman_penilaian(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $reg = $this->pendaftar('SPMB-2027-0003');

        Livewire::test(ListSpmbNilaiQurans::class)
            ->callAction('inputNilai', data: ['nomor' => '2027-3'])
            ->assertHasNoActionErrors()
            ->assertRedirect(SpmbNilaiQuranResource::getUrl('edit', [
                'record' => SpmbNilaiQuran::where('registration_id', $reg->id)->value('id'),
            ]));

        $nilai = SpmbNilaiQuran::where('registration_id', $reg->id)->first();
        $this->assertNotNull($nilai, 'baris nilai tidak dibuat');
        $this->assertSame('Guru Penguji', $nilai->penilai);

        $this->assertDatabaseHas('spmb_nilai_quran', ['registration_id' => $reg->id]);
    }

    public function test_input_nilai_dua_kali_tidak_menggandakan_baris(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $reg = $this->pendaftar('SPMB-2027-0004');

        Livewire::test(ListSpmbNilaiQurans::class)->callAction('inputNilai', data: ['nomor' => 'SPMB-2027-0004']);
        Livewire::test(ListSpmbNilaiQurans::class)->callAction('inputNilai', data: ['nomor' => 'SPMB-2027-0004']);

        $this->assertSame(1, SpmbNilaiQuran::where('registration_id', $reg->id)->count());
    }

    public function test_halaman_penilaian_menampilkan_kartu_siswa_dan_bisa_menyimpan(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $reg = $this->pendaftar('SPMB-2027-0005');
        $nilai = SpmbNilaiQuran::create(['registration_id' => $reg->id, 'penilai' => 'Guru Penguji']);

        Livewire::test(EditSpmbNilaiQuran::class, ['record' => $nilai->getKey()])
            ->assertOk()
            ->assertSee($reg->nomor)
            ->assertSee($reg->nama_siswa)
            ->assertSee('Baca Al-Qur')
            ->fillForm([
                'baca_tajwid' => 90, 'baca_makhraj' => 80, 'baca_kelancaran' => 70,
                'tulis_ketepatan' => 80, 'tulis_kerapian' => 60,
                'sholat_bacaan' => 90, 'sholat_fiqih' => 90, 'sholat_gerakan' => 90,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $segar = $nilai->fresh();
        $this->assertSame(80, $segar->skor_baca);
        $this->assertSame(70, $segar->skor_tulis);
        $this->assertSame(90, $segar->skor_sholat);
        $this->assertSame(80, $segar->skor_btq);
        $this->assertTrue($segar->lengkap);
    }

    public function test_tabel_nilai_hanya_menampilkan_kolom_penting(): void
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('kelola'));

        $this->pendaftar('SPMB-2027-0006');

        Livewire::test(ListSpmbNilaiQurans::class)
            ->assertOk()
            ->assertSee('Pendaftar')
            ->assertSee('BTQ')
            ->assertSee('Penguji')
            ->assertSee('Input nilai');
    }
}
