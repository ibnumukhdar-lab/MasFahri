@extends('layouts.publik')
@section('judul', 'Pengumuman Hasil — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Seleksi Penerimaan', 'judul' => 'Pengumuman Hasil', 'sub' => 'Hasil seleksi mempertimbangkan seluruh paket tes yang wajib diselesaikan.'])

<x-seksi judul="Hasil Seleksi">
  <div class="max-w-2xl mx-auto">

    <div class="kartu p-5 flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <p class="font-bold text-navy">{{ $registrasi->nama_siswa }}</p>
        <p class="text-slate-500 text-[12.5px]">{{ $registrasi->nomor }} · {{ $registrasi->tahun_ajaran }}</p>
      </div>
      <span class="chip">Tes: {{ $progres['selesai'] }} / {{ $progres['total'] }} paket</span>
    </div>

    @if (! $diumumkan)
      {{-- ============ BELUM DIUMUMKAN ============ --}}
      <div class="kartu p-6 text-center mb-4">
        <div class="text-3xl mb-2">⏳</div>
        <p class="font-extrabold text-navy text-[16px] mb-1">Hasil belum diumumkan</p>
        <p class="text-slate-500 text-[13px] leading-relaxed">
          Panitia belum menetapkan hasil seleksi Anda. Pantau halaman ini dan halaman cek status SPMB.
        </p>
      </div>

      @if (! $progres['tuntas'] && $progres['total'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5 text-[13px] text-amber-800 mb-4">
          Masih ada <b>{{ $progres['kurang'] }} paket</b> yang belum Anda selesaikan.
          <a href="{{ route('spmb.pendaftar.tes') }}" class="underline font-semibold">Buka tes</a>.
        </div>
      @endif
    @else
      {{-- ============ SUDAH DIUMUMKAN ============ --}}
      @php $lulus = $registrasi->status === 'diterima'; @endphp
      <div class="kartu p-6 text-center mb-4" style="border-top:4px solid {{ $lulus ? '#2f7a5b' : '#9c2f4f' }}">
        <p class="text-[11px] uppercase tracking-wide font-bold text-slate-400 mb-1">Status seleksi</p>
        <p class="text-2xl font-extrabold {{ $lulus ? 'text-emerald-700' : 'text-rose-700' }} mb-2">
          {{ $lulus ? 'DITERIMA' : 'TIDAK DITERIMA' }}
        </p>
        <p class="text-[13px] text-slate-600">
          {{ $lulus ? 'Selamat! Anda diterima sebagai calon peserta didik baru.' : 'Terima kasih atas keikutsertaan Anda pada seleksi ini.' }}
        </p>
      </div>

      <div class="bg-navy-soft rounded-xl p-4 text-center mb-4">
        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Nilai akhir (rata-rata paket wajib)</p>
        <p class="text-3xl font-extrabold text-navy">{{ $progres['rata'] ?? '—' }}</p>
      </div>

      @if ($registrasi->pesan_admin)
        <div class="kartu p-5 mb-4">
          <p class="text-[11px] uppercase tracking-wide font-bold text-slate-400 mb-1.5">Pesan panitia</p>
          <p class="text-[13.5px] text-slate-700 leading-relaxed whitespace-pre-line">{{ $registrasi->pesan_admin }}</p>
        </div>
      @endif
    @endif

    {{-- ============ RINCIAN PER PAKET ============ --}}
    @if ($ujian->count())
      <div class="kartu overflow-hidden mb-4">
        <div class="px-5 py-3 border-b border-slate-100">
          <p class="text-[11px] uppercase tracking-wide font-bold text-slate-400">Nilai per paket</p>
        </div>
        <table class="w-full text-[13px]">
          <thead class="bg-navy-soft text-slate-500 text-[11px] uppercase">
            <tr>
              <th class="text-left px-4 py-2.5 font-bold">Mata pelajaran</th>
              <th class="text-center px-3 py-2.5 font-bold">Skor</th>
              <th class="text-center px-3 py-2.5 font-bold">Benar</th>
              <th class="text-center px-3 py-2.5 font-bold">KKM</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($ujian as $u)
              <tr class="border-t border-slate-100">
                <td class="px-4 py-3">
                  <p class="font-semibold text-navy">{{ $u->paket?->mata_pelajaran ?: ($u->paket?->judul ?? 'Paket') }}</p>
                  <p class="text-[11.5px] text-slate-500">{{ $u->paket?->judul }}</p>
                </td>
                <td class="px-3 py-3 text-center font-extrabold text-navy">
                  {{ $u->status === 'selesai' ? $u->skor : '—' }}
                </td>
                <td class="px-3 py-3 text-center text-slate-600">
                  {{ $u->status === 'selesai' ? $u->benar . '/' . $u->total : '—' }}
                </td>
                <td class="px-3 py-3 text-center">
                  @if ($u->paket?->kkm && $u->status === 'selesai')
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $u->skor >= $u->paket->kkm ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                      {{ $u->skor >= $u->paket->kkm ? 'Lulus' : 'Belum' }} ({{ $u->paket->kkm }})
                    </span>
                  @else
                    <span class="text-slate-400">—</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <div class="flex flex-wrap gap-2 justify-center">
      <a href="{{ route('spmb.pendaftar.dasbor') }}" class="text-[13px] font-semibold text-navy border border-slate-200 rounded-xl px-4 py-2.5">Dasbor</a>
      <a href="{{ route('spmb.pendaftar.kartu') }}" class="text-[13px] font-semibold text-navy border border-slate-200 rounded-xl px-4 py-2.5">Kartu Tes</a>
      @if (! $diumumkan)
        <a href="{{ route('spmb.pendaftar.tes') }}" class="text-[13px] font-semibold text-white bg-navy rounded-xl px-4 py-2.5">Buka Tes CBT</a>
      @endif
    </div>
  </div>
</x-seksi>
@endsection
