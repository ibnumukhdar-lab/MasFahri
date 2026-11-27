<?php

namespace App\Filament\Pages;

use App\Models\Registration;
use App\Models\SpmbNilaiQuran;
use App\Models\SpmbPaketSoal;
use App\Models\SpmbUjian;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Rekap hasil tes SPMB untuk PANITIA:
 * nilai CBT per mata pelajaran, nilai BTQ (baca, tulis, praktek sholat)
 * beserta kriteria, dan tombol keputusan panitia.
 */
class RekapNilaiSpmb extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $navigationLabel = 'Rekap Hasil Tes';

    protected static ?string $title = 'Rekap Hasil Tes';

    protected static ?string $slug = 'rekap-nilai-spmb';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.rekap-nilai-spmb';

    /** Kata kunci pencarian nama / nomor pendaftaran. */
    public string $cari = '';

    /** ID pendaftar yang form BTQ-nya sedang dibuka (null = tertutup). */
    public ?int $bukaId = null;

    /** Isian form BTQ yang sedang dibuka. */
    public array $form = [];

    public function bukaBtq(int $id): void
    {
        $q = SpmbNilaiQuran::firstOrCreate(['registration_id' => $id]);

        $this->bukaId = $id;
        $this->form = [
            'baca_tajwid' => $q->baca_tajwid,
            'baca_makhraj' => $q->baca_makhraj,
            'baca_kelancaran' => $q->baca_kelancaran,
            'tulis_ketepatan' => $q->tulis_ketepatan,
            'tulis_kerapian' => $q->tulis_kerapian,
            'sholat_bacaan' => $q->sholat_bacaan,
            'sholat_fiqih' => $q->sholat_fiqih,
            'sholat_gerakan' => $q->sholat_gerakan,
            'penilai' => $q->penilai,
            'catatan' => $q->catatan,
        ];
    }

    public function tutupBtq(): void
    {
        $this->bukaId = null;
        $this->form = [];
    }

    public function simpanBtqForm(): void
    {
        if (! $this->bukaId) {
            return;
        }

        $aturan = [];
        $pesan = [];
        $label = [];
        foreach (SpmbNilaiQuran::ASPEK as $aspek) {
            foreach ($aspek['kriteria'] as $k => $nama) {
                $aturan["form.$k"] = ['nullable', 'integer', 'min:0', 'max:100'];
                $label["form.$k"] = $nama;
                $pesan["form.$k.integer"] = "Nilai $nama harus angka bulat.";
                $pesan["form.$k.min"] = "Nilai $nama minimal 0.";
                $pesan["form.$k.max"] = "Nilai $nama maksimal 100.";
            }
        }
        $aturan['form.penilai'] = ['nullable', 'string', 'max:120'];
        $aturan['form.catatan'] = ['nullable', 'string', 'max:1000'];
        $label['form.penilai'] = 'Nama penilai';
        $label['form.catatan'] = 'Catatan';
        $pesan['form.penilai.max'] = 'Nama penilai maksimal 120 karakter.';

        $this->validate($aturan, $pesan, $label);

        $q = SpmbNilaiQuran::firstOrCreate(['registration_id' => $this->bukaId]);

        foreach (array_keys($this->form) as $k) {
            if (in_array($k, ['penilai', 'catatan'], true)) {
                $q->{$k} = $this->form[$k] !== '' ? $this->form[$k] : null;
            } else {
                $q->{$k} = ($this->form[$k] === '' || $this->form[$k] === null) ? null : (int) $this->form[$k];
            }
        }
        $q->nilai_baca = null;
        $q->nilai_tulis = null;
        $q->save();

        Notification::make()
            ->title('Nilai BTQ tersimpan')
            ->body('Baca: '.($q->skor_baca ?? '—').' · Tulis: '.($q->skor_tulis ?? '—')
                .' · Sholat: '.($q->skor_sholat ?? '—').' · BTQ: '.($q->skor_btq ?? '—'))
            ->success()
            ->send();

        $this->tutupBtq();
    }

    public function terima(int $id): void
    {
        $r = Registration::find($id);
        if (! $r) {
            return;
        }

        $r->status = 'diterima';
        $r->save();

        Notification::make()
            ->title($r->nama_siswa.' diterima')
            ->body('Keputusan panitia tersimpan.')
            ->success()
            ->send();
    }

    public function tolak(int $id): void
    {
        $r = Registration::find($id);
        if (! $r) {
            return;
        }

        $r->status = 'ditolak';
        $r->save();

        Notification::make()
            ->title($r->nama_siswa.' ditandai tidak diterima')
            ->body('Keputusan panitia tersimpan.')
            ->warning()
            ->send();
    }

    public function batalkan(int $id): void
    {
        $r = Registration::find($id);
        if (! $r) {
            return;
        }

        $r->status = 'tes';
        $r->save();

        Notification::make()
            ->title('Keputusan dibatalkan')
            ->body($r->nama_siswa.' kembali ke tahap tes.')
            ->send();
    }

    public function getViewData(): array
    {
        $paket = SpmbPaketSoal::query()
            ->where('aktif', true)->where('wajib', true)
            ->orderBy('urutan')->orderBy('id')->get();

        $registrasi = Registration::query()
            ->whereIn('status', ['tes', 'diterima', 'ditolak'])
            ->when(trim($this->cari) !== '', function ($q) {
                $k = '%'.trim($this->cari).'%';
                $q->where(fn ($w) => $w->where('nama_siswa', 'like', $k)
                    ->orWhere('nomor', 'like', $k)
                    ->orWhere('nama_wali', 'like', $k));
            })
            ->orderBy('nomor')
            ->get();

        $ids = $registrasi->pluck('id');

        $ujian = SpmbUjian::query()
            ->whereIn('registration_id', $ids)
            ->where('status', 'selesai')
            ->get()
            ->groupBy('registration_id');

        $quran = SpmbNilaiQuran::query()
            ->whereIn('registration_id', $ids)
            ->get()->keyBy('registration_id');

        $baris = $registrasi->map(function (Registration $r) use ($paket, $ujian, $quran) {
            $nilai = [];
            foreach ($paket as $p) {
                $u = collect($ujian[$r->id] ?? [])->firstWhere('paket_id', $p->id);
                $nilai[$p->id] = $u?->skor;
            }

            $terisi = array_filter($nilai, fn ($v) => $v !== null);
            $q = $quran[$r->id] ?? null;

            return [
                'registrasi' => $r,
                'nilai' => $nilai,
                'rata' => $terisi ? (int) round(array_sum($terisi) / count($terisi)) : null,
                'dikerjakan' => count($terisi),
                'lengkap' => $paket->count() > 0 && count($terisi) === $paket->count(),
                'baca' => $q?->skor_baca,
                'tulis' => $q?->skor_tulis,
                'sholat' => $q?->skor_sholat,
                'btq' => $q?->skor_btq,
                'btq_lengkap' => $q?->lengkap ?? false,
                'catatan' => $q?->catatan,
            ];
        });

        return [
            'paket' => $paket,
            'baris' => $baris,
            'aspek' => SpmbNilaiQuran::ASPEK,
            'dinilai' => $this->bukaId ? Registration::find($this->bukaId) : null,
            'jumlahLengkap' => $baris->where('lengkap', true)->count(),
            'jumlahDiterima' => $baris->where('registrasi.status', 'diterima')->count(),
            'jumlahDitolak' => $baris->where('registrasi.status', 'ditolak')->count(),
        ];
    }
}
