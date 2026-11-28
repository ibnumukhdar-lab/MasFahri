@extends('layouts.spmb')
@section('judul', 'Hari Tes — SPMB SMA IT Arafah')
@section('isi')
  'kecil' => 'Tes Penerimaan',
  'judul' => 'Hari Tes',
  'sub' => $hariTes ? 'Jadwal tes: '.$hariTes.'. Dua tahap: Tes Akademik lalu Tes BTQ.' : 'Jadwal tes ditetapkan panitia.',
])

<div class="flex flex-wrap items-end justify-between gap-2 mb-4">
  <div>
    <p class="text-[10.5px] font-bold uppercase tracking-[.16em] text-slate-400">Tahap seleksi</p>
    <h1 class="text-[21px] md:text-[24px] font-semibold text-navy mt-1 leading-tight">Tes Masuk</h1>
    <p class="text-[12.5px] text-slate-500 mt-1">{{ $hariTes ? 'Jadwal tes: '.$hariTes : 'Jadwal tes ditetapkan panitia' }} · dua tahap: Tes Akademik lalu Tes BTQ.</p>
  </div>
  <a href="{{ route('spmb.pendaftar.dasbor') }}" class="text-[12px] font-semibold text-navy border border-slate-200 rounded-lg px-3 py-2">Kembali ke ringkasan</a>
</div>

<x-seksi judul="Alur Tes Anda">
  <div class="max-w-2xl mx-auto">
    @if ($errors->any())
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    {{-- ===== Identitas & progres ===== --}}
    <div class="kartu p-5 md:p-6 text-[13.5px] mb-5">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
          <p class="font-bold text-navy">{{ $registrasi->nama_siswa }}</p>
          <p class="text-slate-500 text-[12.5px]">{{ $registrasi->nomor }}</p>
        </div>
        <div class="text-right">
          <p class="text-[11px] uppercase tracking-wide text-slate-500 font-bold">Tes Akademik</p>
          <p class="font-extrabold text-navy text-[16px]">{{ $selesaiPaket }} / {{ $jumlahPaket }} paket</p>
        </div>
      </div>

      @if ($jumlahPaket > 0)
        <div class="mt-3 h-2 rounded-full overflow-hidden" style="background:#e8eef7">
          <div class="h-full" style="width:{{ (int) round($selesaiPaket / max(1, $jumlahPaket) * 100) }}%;background:{{ $tuntas ? '#2f7a5b' : '#1f3a5f' }}"></div>
        </div>
        <p class="text-[12px] mt-2 {{ $tuntas ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
          @if ($tuntas)
            ✅ Semua paket Tes Akademik selesai — lanjut ke tahap Tes BTQ.
          @else
            Selesaikan <b>semua paket</b>, satu per satu. Setiap paket bisa dikerjakan berurutan.
          @endif
        </p>
      @endif
    </div>

    {{-- ================= 1. TES AKADEMIK ================= --}}
    <div class="flex items-center gap-2.5 mb-3">
      <span class="w-7 h-7 rounded-full bg-navy text-white text-[12px] font-bold flex items-center justify-center shrink-0">1</span>
      <div>
        <p class="font-extrabold text-navy text-[15px] leading-tight">Tes Akademik</p>
        <p class="text-[12px] text-slate-500">{{ $jumlahPaket }} paket pilihan ganda · semua wajib diselesaikan</p>
      </div>
    </div>

    @if ($jumlahPaket === 0)
      <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5 text-[13px] text-amber-800 mb-4">
        Belum ada paket tes yang dibuka panitia. Coba lagi sesuai jadwal di kartu tes.
      </div>
    @endif

    @php
      $berikut = $daftar->first(fn ($d) => ! $d['selesai'] && $d['terbuka']);
      $berikutId = $berikut ? $berikut['paket']->id : null;
    @endphp

    <div class="space-y-3">
      @foreach ($daftar as $i => $d)
        @php $p = $d['paket']; $u = $d['ujian']; @endphp
        <div class="kartu p-5" @if ($p->id === $berikutId) style="border-color:#1f3a5f;box-shadow:0 0 0 2px rgba(31,58,95,.08)" @endif>
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="text-[11px] uppercase tracking-wide text-slate-400 font-bold">
                Paket {{ $i + 1 }}@if ($p->id === $berikutId) · <span class="text-navy">berikutnya</span>@endif
              </p>
              <p class="font-extrabold text-navy text-[15px] leading-snug">{{ $p->judul }}</p>
              @if ($p->mata_pelajaran)
                <p class="text-[12.5px] text-slate-500">{{ $p->mata_pelajaran }}</p>
              @endif
              <p class="text-[12.5px] text-slate-600 mt-1">
                {{ $p->durasi_menit }} menit · {{ $p->soal()->count() }} soal pilihan ganda
              </p>
              <p class="text-[12px] text-slate-500 mt-0.5">Jadwal: {{ $p->jadwal }}</p>
            </div>

            @if ($d['selesai'])
              <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">Selesai</span>
            @elseif ($d['sedang'])
              <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-700">Sedang berjalan</span>
            @elseif (! $d['terbuka'])
              <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">Belum dibuka</span>
            @else
              <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-full bg-navy-soft text-navy">Siap dikerjakan</span>
            @endif
          </div>

          @if ($d['selesai'])
            <p class="text-[12.5px] text-slate-500 mt-3">
              Sudah dikerjakan{{ $u->selesai_pada ? ' pada '.$u->selesai_pada->format('d M Y H:i') : '' }}. Nilai direkap panitia.
            </p>
          @elseif ($d['sedang'])
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mt-3 text-[12.5px] text-amber-800">
              Sisa waktu ± {{ intdiv($u->sisa_detik, 60) }} menit {{ $u->sisa_detik % 60 }} detik. Jawaban yang belum dikirim akan hilang bila waktu habis.
            </div>
            <a href="{{ route('spmb.pendaftar.kerjakan', ['paket' => $p->id]) }}" class="block text-center mt-3 bg-emerald-600 text-white rounded-xl px-5 py-3 font-bold">Lanjutkan Paket Ini</a>
          @elseif (! $d['terbuka'])
            <p class="text-[12.5px] text-slate-500 mt-3">Paket ini terbuka sesuai jadwal di atas — kembali lagi pada waktunya.</p>
          @else
            <form action="{{ route('spmb.pendaftar.tes.mulai') }}" method="post" class="mt-4">
              @csrf
              <input type="hidden" name="paket_id" value="{{ $p->id }}">
              <label class="block mb-1.5 font-medium text-navy text-[13px]">Kode tes</label>
              <div class="flex gap-2">
                <input name="kode" value="{{ old('kode') }}" maxlength="12" placeholder="K7X2QM"
                       class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5 font-mono text-[15px] tracking-[.25em] uppercase"
                       style="font-family:'Courier New',monospace" autocomplete="off" required>
                <button class="bg-emerald-600 text-white rounded-xl px-4 py-2.5 font-bold text-[13px] whitespace-nowrap">Mulai</button>
              </div>
              <p class="text-[11.5px] text-slate-500 mt-2">Setelah ditekan, waktu paket ini mulai berjalan. Selesaikan sebelum berpindah paket.</p>
            </form>
          @endif
        </div>
      @endforeach
    </div>

    {{-- ================= 2. TES BTQ ================= --}}
    <div class="flex items-center gap-2.5 mt-7 mb-3">
      <span class="w-7 h-7 rounded-full {{ $tuntas ? 'bg-emerald-600' : 'bg-slate-300' }} text-white text-[12px] font-bold flex items-center justify-center shrink-0">2</span>
      <div>
        <p class="font-extrabold text-navy text-[15px] leading-tight">Tes BTQ</p>
        <p class="text-[12px] text-slate-500">Baca Tulis Al-Qur'an · dinilai Ustadz/Ustadzah penguji</p>
      </div>
    </div>

    <div class="kartu p-5">
      @if (! $tuntas)
        <div class="flex items-start gap-3">
          <span class="text-lg">🔒</span>
          <div>
            <p class="font-bold text-navy text-[14px] mb-1">Belum bisa dimulai</p>
            <p class="text-[12.5px] text-slate-600 leading-relaxed">
              Selesaikan dulu semua paket Tes Akademik ({{ $selesaiPaket }} dari {{ $jumlahPaket }} sudah selesai).
              Setelah tuntas, petunjuk menuju pos Tes BTQ muncul di sini.
            </p>
          </div>
        </div>
      @elseif ($btqDinilai)
        <div class="flex items-start gap-3">
          <span class="text-lg">✅</span>
          <div>
            <p class="font-bold text-emerald-700 text-[14px] mb-1">Tes BTQ sudah dinilai</p>
            <p class="text-[12.5px] text-slate-600">Nilai dari Ustadz/Ustadzah penguji sudah masuk ke panitia.</p>
          </div>
        </div>
      @else
        <div class="rounded-xl p-4" style="background:#eef6f2;border:1px solid #cfe6da">
          <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-800 mb-1">Tahap 2 — silakan menuju pos BTQ</p>
          <p class="font-extrabold text-navy text-[15px] mb-1.5">Tunjukkan kartu tes ini kepada Ustadz/Ustadzah penguji BTQ</p>
          <p class="text-[12.5px] text-slate-600 leading-relaxed">
            Penguji akan menilai Baca Al-Qur'an, Tulis Arab, dan Praktek Sholat. Anda tidak perlu mengisi apa pun —
            nilai diisi langsung oleh penguji melalui panel panitia.
          </p>

          <div class="flex flex-wrap items-center gap-3 mt-3">
            <div class="bg-white rounded-lg border border-emerald-200 px-3 py-2">
              <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Kode tes</p>
              <p class="font-black text-navy text-[16px] tracking-[.2em]" style="font-family:'Courier New',monospace">{{ $registrasi->kode_tes }}</p>
            </div>
            <div class="bg-white rounded-lg border border-emerald-200 px-3 py-2">
              <p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Nomor pendaftaran</p>
              <p class="font-black text-navy text-[13px]">{{ $registrasi->nomor }}</p>
            </div>
          </div>

          <a href="{{ route('spmb.pendaftar.kartu') }}" class="inline-block mt-3 bg-navy text-white rounded-xl px-5 py-2.5 font-semibold text-[13px]">
            Lihat Kartu Tes (Cetak)
          </a>
        </div>
      @endif
    </div>

    <p class="text-center mt-5"><a href="{{ route('spmb.pendaftar.dasbor') }}" class="text-[13px] text-slate-500 underline">Kembali ke dasbor</a></p>
  </div>
</x-seksi>
@endsection
