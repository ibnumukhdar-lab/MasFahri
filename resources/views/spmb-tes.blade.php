@extends('layouts.publik')
@section('judul', 'Tes CBT — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Tes Penerimaan', 'judul' => 'Tes CBT', 'sub' => 'Setiap mata pelajaran punya paket sendiri. Semua paket wajib diselesaikan pada hari tes.'])

<x-seksi judul="Paket Tes Anda">
  <div class="max-w-2xl mx-auto">
    @if ($errors->any())
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="kartu p-5 md:p-6 text-[13.5px] mb-4">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
          <p class="font-bold text-navy">{{ $registrasi->nama_siswa }}</p>
          <p class="text-slate-500 text-[12.5px]">{{ $registrasi->nomor }}</p>
        </div>
        <div class="text-right">
          <p class="text-[11px] uppercase tracking-wide text-slate-500 font-bold">Progres</p>
          <p class="font-extrabold text-navy text-[16px]">{{ $selesaiPaket }} / {{ $jumlahPaket }} paket</p>
        </div>
      </div>

      @if ($jumlahPaket > 0)
        <div class="mt-3 h-2 rounded-full overflow-hidden" style="background:#e8eef7">
          <div class="h-full" style="width:{{ (int) round($selesaiPaket / max(1, $jumlahPaket) * 100) }}%;background:{{ $tuntas ? '#2f7a5b' : '#1f3a5f' }}"></div>
        </div>
        <p class="text-[12px] mt-2 {{ $tuntas ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
          @if ($tuntas)
            ✅ Semua paket sudah diselesaikan.
          @else
            Selesaikan <b>semua paket</b> — hasil akhir dihitung dari rata-rata seluruh paket wajib.
          @endif
        </p>
      @endif
    </div>

    @if ($tuntas)
      {{-- ================= RINGKASAN AKHIR ================= --}}
      <div class="kartu p-6 text-center mb-4">
        <div class="text-4xl mb-2">🏆</div>
        <p class="font-extrabold text-navy text-[17px] mb-1">Tes selesai — semua paket tuntas</p>
        <p class="text-slate-500 text-[12.5px] mb-5">Hasil sudah masuk ke panitia. Terima kasih!</p>

        <div class="bg-navy-soft rounded-xl p-4 max-w-xs mx-auto mb-5">
          <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Nilai akhir (rata-rata)</p>
          <p class="text-3xl font-extrabold text-navy">{{ $rataRata }}</p>
        </div>

        <a href="{{ route('spmb.pendaftar.kartu') }}" class="inline-block bg-navy text-white rounded-xl px-5 py-2.5 font-semibold">Lihat Kartu Tes</a>
      </div>
    @endif

    @if ($jumlahPaket === 0)
      <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5 text-[13px] text-amber-800">
        Belum ada paket tes yang dibuka panitia. Coba lagi sesuai jadwal di kartu tes.
      </div>
    @endif

    {{-- ================= KARTU PER PAKET ================= --}}
    <div class="space-y-3">
      @foreach ($daftar as $i => $d)
        @php $p = $d['paket']; $u = $d['ujian']; @endphp
        <div class="kartu p-5">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="text-[11px] uppercase tracking-wide text-slate-400 font-bold">Paket {{ $i + 1 }}</p>
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
            <div class="grid grid-cols-3 gap-3 mt-4">
              <div class="bg-navy-soft rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold uppercase text-slate-500 mb-0.5">Skor</p>
                <p class="text-xl font-extrabold text-navy">{{ $u->skor }}</p>
              </div>
              <div class="bg-navy-soft rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold uppercase text-slate-500 mb-0.5">Benar</p>
                <p class="text-xl font-extrabold text-navy">{{ $u->benar }}/{{ $u->total }}</p>
              </div>
              <div class="bg-navy-soft rounded-xl p-3 text-center">
                <p class="text-[10px] font-bold uppercase text-slate-500 mb-0.5">Selesai</p>
                <p class="text-[12px] font-bold text-navy leading-tight mt-1">{{ $u->selesai_pada?->format('d M H:i') }}</p>
              </div>
            </div>
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

    <p class="text-center mt-5"><a href="{{ route('spmb.pendaftar.dasbor') }}" class="text-[13px] text-slate-500 underline">Kembali ke dasbor</a></p>
  </div>
</x-seksi>
@endsection
