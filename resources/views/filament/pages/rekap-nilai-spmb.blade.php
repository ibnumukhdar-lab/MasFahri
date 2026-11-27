<x-filament-panels::page>
    <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            <span class="font-semibold">{{ $baris->count() }}</span> pendaftar ·
            <span class="font-semibold">{{ $jumlahLengkap }}</span> menuntaskan semua paket CBT ·
            <span class="font-semibold text-success-700">{{ $jumlahDiterima }}</span> diterima ·
            <span class="font-semibold text-danger-700">{{ $jumlahDitolak }}</span> tidak diterima
        </div>

        <div class="w-full sm:w-72">
            <input type="search"
                   wire:model.live.debounce.400ms="cari"
                   placeholder="Cari nama / nomor pendaftaran…"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-white/10 dark:bg-gray-900">
        </div>
    </div>

    <x-filament::section>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10 text-[11px] uppercase tracking-wide text-gray-500">
                        <th class="py-2 pr-3">No</th>
                        <th class="py-2 pr-3">Nama siswa</th>
                        @foreach ($paket as $p)
                            <th class="py-2 px-2 text-center">
                                {{ $p->mata_pelajaran ?: $p->judul }}
                                @if ($p->kkm)
                                    <span class="block text-[10px] font-normal normal-case text-gray-400">KKM {{ $p->kkm }}</span>
                                @endif
                            </th>
                        @endforeach
                        <th class="py-2 px-2 text-center">Rata CBT</th>
                        <th class="py-2 px-2 text-center">BTQ baca</th>
                        <th class="py-2 px-2 text-center">BTQ tulis</th>
                        <th class="py-2 px-2 text-center">Status</th>
                        <th class="py-2 pl-3 text-right">Keputusan panitia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $i => $b)
                        @php $r = $b['registrasi']; @endphp
                        <tr class="border-b border-gray-100 dark:border-white/5 align-middle">
                            <td class="py-2.5 pr-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-2.5 pr-3">
                                <div class="font-semibold">{{ $r->nama_siswa }}</div>
                                <div class="text-xs text-gray-500">{{ $r->nomor }} · {{ $r->tahun_ajaran }}</div>
                            </td>

                            @foreach ($paket as $p)
                                @php
                                    $n = $b['nilai'][$p->id] ?? null;
                                    $bawah = $n !== null && $p->kkm && $n < $p->kkm;
                                @endphp
                                <td @class([
                                    'py-2.5 px-2 text-center font-bold',
                                    'text-danger-600' => $bawah,
                                    'text-gray-400' => $n === null,
                                ])>{{ $n ?? '—' }}</td>
                            @endforeach

                            <td class="py-2.5 px-2 text-center">
                                <span class="font-extrabold">{{ $b['rata'] ?? '—' }}</span>
                                @if (! $b['lengkap'])
                                    <span class="block text-[10px] text-warning-600">{{ $b['dikerjakan'] }}/{{ $paket->count() }} paket</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-2 text-center">
                                <input type="number" min="0" max="100" inputmode="numeric"
                                       value="{{ $b['baca'] }}"
                                       wire:change="simpanBtq({{ $r->id }}, 'nilai_baca', $event.target.value)"
                                       class="w-16 rounded-md border border-gray-300 px-1.5 py-1 text-center text-sm dark:border-white/10 dark:bg-gray-900"
                                       placeholder="—">
                            </td>
                            <td class="py-2.5 px-2 text-center">
                                <input type="number" min="0" max="100" inputmode="numeric"
                                       value="{{ $b['tulis'] }}"
                                       wire:change="simpanBtq({{ $r->id }}, 'nilai_tulis', $event.target.value)"
                                       class="w-16 rounded-md border border-gray-300 px-1.5 py-1 text-center text-sm dark:border-white/10 dark:bg-gray-900"
                                       placeholder="—">
                            </td>

                            <td class="py-2.5 px-2 text-center">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold whitespace-nowrap',
                                    'bg-gray-100 text-gray-700' => $r->status === 'tes',
                                    'bg-success-50 text-success-700' => $r->status === 'diterima',
                                    'bg-danger-50 text-danger-700' => $r->status === 'ditolak',
                                ])>
                                    {{ \App\Models\Registration::STATUS[$r->status] ?? $r->status }}
                                </span>
                            </td>

                            <td class="py-2.5 pl-3 text-right whitespace-nowrap">
                                @if ($r->status !== 'diterima')
                                    <button type="button"
                                            wire:click="terima({{ $r->id }})"
                                            wire:confirm="Terima {{ $r->nama_siswa }}? Status akan terlihat oleh siswa."
                                            class="rounded-lg bg-success-600 px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-success-700">
                                        Terima
                                    </button>
                                @endif
                                @if ($r->status !== 'ditolak')
                                    <button type="button"
                                            wire:click="tolak({{ $r->id }})"
                                            wire:confirm="Tandai {{ $r->nama_siswa }} tidak diterima?"
                                            class="rounded-lg bg-danger-600 px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-danger-700">
                                        Tolak
                                    </button>
                                @endif
                                @if ($r->status !== 'tes')
                                    <button type="button"
                                            wire:click="batalkan({{ $r->id }})"
                                            wire:confirm="Batalkan keputusan untuk {{ $r->nama_siswa }}?"
                                            class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-[11px] font-semibold text-gray-600 hover:bg-gray-50 dark:border-white/10 dark:text-gray-300">
                                        Batalkan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 7 + $paket->count() }}" class="py-6 text-center text-gray-500">
                                Tidak ada pendaftar yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="mt-3 text-xs text-gray-500">
            Nilai CBT diisi otomatis dari pengerjaan siswa. Nilai BTQ diisi panitia (tersimpan saat keluar dari kotak).
            Keputusan diterima / tidak diterima sepenuhnya wewenang panitia.
        </p>
    </x-filament::section>
</x-filament-panels::page>
