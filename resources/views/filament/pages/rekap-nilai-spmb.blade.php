<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Rekap Nilai Seleksi</x-slot>
        <x-slot name="description">
            Nilai CBT per mata pelajaran dan nilai baca tulis Al-Qur'an. Halaman ini hanya untuk panitia.
            Siswa hanya melihat status "selesai" tanpa nilai.
        </x-slot>

        <div class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            {{ $baris->count() }} pendaftar di tahap tes · {{ $jumlahLengkap }} sudah menuntaskan semua paket CBT
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10 text-xs uppercase tracking-wide text-gray-500">
                        <th class="py-2 pr-3">No</th>
                        <th class="py-2 pr-3">Nomor</th>
                        <th class="py-2 pr-3">Nama</th>
                        <th class="py-2 pr-3">Status</th>
                        @foreach ($paket as $p)
                            <th class="py-2 px-2 text-center">
                                {{ $p->mata_pelajaran ?: $p->judul }}
                                @if ($p->kkm)
                                    <span class="block text-[10px] font-normal normal-case text-gray-400">KKM {{ $p->kkm }}</span>
                                @endif
                            </th>
                        @endforeach
                        <th class="py-2 px-2 text-center">Rata CBT</th>
                        <th class="py-2 px-2 text-center">Baca</th>
                        <th class="py-2 px-2 text-center">Tulis</th>
                        <th class="py-2 pl-3">Catatan Qur'an</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $i => $b)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="py-2.5 pr-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-2.5 pr-3 font-mono text-xs">{{ $b['registrasi']->nomor }}</td>
                            <td class="py-2.5 pr-3">
                                <div class="font-semibold">{{ $b['registrasi']->nama_siswa }}</div>
                                <div class="text-xs text-gray-500">{{ $b['registrasi']->tahun_ajaran }}</div>
                            </td>
                            <td class="py-2.5 pr-3">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                    'bg-gray-100 text-gray-700' => $b['registrasi']->status === 'tes',
                                    'bg-success-50 text-success-700' => $b['registrasi']->status === 'diterima',
                                    'bg-danger-50 text-danger-700' => $b['registrasi']->status === 'ditolak',
                                ])>
                                    {{ \App\Models\Registration::STATUS[$b['registrasi']->status] ?? $b['registrasi']->status }}
                                </span>
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
                                ])>
                                    {{ $n ?? '—' }}
                                </td>
                            @endforeach
                            <td class="py-2.5 px-2 text-center font-extrabold">
                                {{ $b['rata'] ?? '—' }}
                                @if (! $b['lengkap'] && $b['dikerjakan'] > 0)
                                    <span class="block text-[10px] font-normal text-warning-600">{{ $b['dikerjakan'] }}/{{ $paket->count() }} paket</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-2 text-center">{{ $b['baca'] ?? '—' }}</td>
                            <td class="py-2.5 px-2 text-center">{{ $b['tulis'] ?? '—' }}</td>
                            <td class="py-2.5 pl-3 text-xs text-gray-500">{{ $b['catatan'] ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 7 + $paket->count() }}" class="py-6 text-center text-gray-500">
                                Belum ada pendaftar di tahap tes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
