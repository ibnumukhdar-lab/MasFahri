<?php

namespace App\Filament\Pages;

use App\Models\Registration;
use App\Models\SpmbNilaiQuran;
use App\Models\SpmbPaketSoal;
use App\Models\SpmbUjian;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Rekap nilai SPMB untuk PANITIA (tidak tampil ke siswa):
 * nilai CBT per paket/mata pelajaran + rata-rata + nilai baca tulis Al-Qur'an.
 */
class RekapNilaiSpmb extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $navigationLabel = 'Rekap Nilai SPMB';

    protected static ?string $title = 'Rekap Nilai SPMB';

    protected static ?string $slug = 'rekap-nilai-spmb';

    protected static string | UnitEnum | null $navigationGroup = 'SPMB & Alumni';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.rekap-nilai-spmb';

    public function getViewData(): array
    {
        $paket = SpmbPaketSoal::query()
            ->where('aktif', true)->where('wajib', true)
            ->orderBy('urutan')->orderBy('id')->get();

        $registrasi = Registration::query()
            ->whereIn('status', ['tes', 'diterima', 'ditolak'])
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

            return [
                'registrasi' => $r,
                'nilai' => $nilai,
                'rata' => $terisi ? (int) round(array_sum($terisi) / count($terisi)) : null,
                'dikerjakan' => count($terisi),
                'lengkap' => $paket->count() > 0 && count($terisi) === $paket->count(),
                'baca' => $quran[$r->id]->nilai_baca ?? null,
                'tulis' => $quran[$r->id]->nilai_tulis ?? null,
                'catatan' => $quran[$r->id]->catatan ?? null,
                'id_quran' => $quran[$r->id]->id ?? null,
            ];
        });

        return [
            'paket' => $paket,
            'baris' => $baris,
            'jumlahLengkap' => $baris->where('lengkap', true)->count(),
        ];
    }
}
