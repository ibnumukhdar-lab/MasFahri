<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\SpmbPaketSoal;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

/**
 * Pengaturan SPMB: tanggal pengumuman hasil.
 * Bila dikosongkan → otomatis H+1 dari hari tes terakhir.
 */
class PengaturanSpmb extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan SPMB';

    protected static ?string $title = 'Pengaturan SPMB';

    protected static ?string $slug = 'pengaturan-spmb';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.pengaturan-spmb';

    public ?string $tanggalPengumuman = '';

    public function mount(): void
    {
        $this->tanggalPengumuman = (string) (Setting::ambil('spmb_tanggal_pengumuman') ?? '');
    }

    public function simpan(): void
    {
        $this->validate([
            'tanggalPengumuman' => ['nullable', 'date'],
        ], [
            'tanggalPengumuman.date' => 'Format tanggal tidak sah.',
        ]);

        if (filled($this->tanggalPengumuman)) {
            Setting::simpan(['spmb_tanggal_pengumuman' => $this->tanggalPengumuman]);
        } else {
            Setting::updateOrCreate(['kunci' => 'spmb_tanggal_pengumuman'], ['nilai' => null]);
            Cache::forget('settings');
        }

        Notification::make()
            ->title('Pengaturan disimpan')
            ->body('Tanggal pengumuman: '.($this->tanggalPengumuman ?: 'otomatis (H+1 hari tes terakhir)'))
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $hari = SpmbPaketSoal::query()
            ->where('aktif', true)->where('wajib', true)
            ->whereNotNull('tanggal_tes')
            ->orderBy('tanggal_tes')
            ->get()
            ->groupBy(fn ($p) => $p->tanggal_tes->toDateString());

        $otomatis = $hari->keys()->sort()->last();

        return [
            'hariTes' => $hari,
            'otomatis' => $otomatis ? \Carbon\Carbon::parse($otomatis)->addDay() : null,
        ];
    }
}
