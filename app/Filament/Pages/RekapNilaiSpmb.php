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
 * daftar siswa, nilai CBT per mata pelajaran, nilai BTQ (baca tulis Al-Qur'an),
 * dan tombol keputusan panitia (terima / tolak).
 * Tidak tampil ke siswa.
 */
class RekapNilaiSpmb extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $navigationLabel = 'Rekap Hasil Tes';

    protected static ?string $title = 'Rekap Hasil Tes';

    protected static ?string $slug = 'rekap-nilai-spmb';

    protected static string | UnitEnum | null $navigationGroup = 'SPMB & Alumni';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.rekap-nilai-spmb';

    /** Kata kunci pencarian nama / nomor pendaftaran. */
    public string $cari = '';

    /** Keputusan panitia: terima. */
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
            ->body('Keputusan panitia tersimpan. Siswa melihat status pada halaman cek status SPMB.')
            ->success()
            ->send();
    }

    /** Keputusan panitia: tidak diterima. */
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

    /** Batalkan keputusan → kembali ke tahap tes. */
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

    /** Simpan nilai BTQ langsung dari tabel (0–100). */
    public function simpanBtq(int $id, string $kolom, $nilai): void
    {
        if (! in_array($kolom, ['nilai_baca', 'nilai_tulis'], true)) {
            return;
        }

        $nilai = trim((string) $nilai);
        if ($nilai === '') {
            $angka = null;
        } elseif (! is_numeric($nilai) || (int) $nilai < 0 || (int) $nilai > 100) {
            Notification::make()
                ->title('Nilai BTQ tidak sah')
                ->body('Isi angka 0–100.')
                ->danger()
                ->send();

            return;
        } else {
            $angka = (int) $nilai;
        }

        $q = SpmbNilaiQuran::firstOrCreate(['registration_id' => $id]);
        $q->{$kolom} = $angka;
        $q->save();

        Notification::make()
            ->title('Nilai BTQ tersimpan')
            ->body(($kolom === 'nilai_baca' ? 'Baca' : 'Tulis').': '.($angka ?? '—'))
            ->success()
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
                'baca' => $q?->nilai_baca,
                'tulis' => $q?->nilai_tulis,
                'catatan' => $q?->catatan,
            ];
        });

        return [
            'paket' => $paket,
            'baris' => $baris,
            'jumlahLengkap' => $baris->where('lengkap', true)->count(),
            'jumlahDiterima' => $baris->where('registrasi.status', 'diterima')->count(),
            'jumlahDitolak' => $baris->where('registrasi.status', 'ditolak')->count(),
        ];
    }
}
