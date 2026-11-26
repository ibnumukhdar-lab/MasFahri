@extends('layouts.publik')
@section('judul', 'Tes CBT — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Tes Penerimaan', 'judul' => 'Tes CBT Potensi Akademik & Minat Bakat', 'sub' => 'Kerjakan dengan jujur dan tenang. Tes hanya bisa dikerjakan satu kali.'])

<x-seksi judul="Pintu Masuk Tes">
  <div class="max-w-xl mx-auto">
    @if ($errors->any())
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="kartu p-6 md:p-7 text-[13.5px]">
      <p class="mb-1"><b>{{ $registrasi->nama_siswa }}</b> · {{ $registrasi->nomor }}</p>

      @if ($ujian && $ujian->status === 'selesai')
        {{-- ============ HASIL ============ --}}
        <div class="text-center py-4">
          <div class="text-4xl mb-2">🏆</div>
          <p class="font-extrabold text-navy text-[17px] mb-1">Tes sudah selesai dikerjakan</p>
          <p class="text-slate-500 text-[12.5px] mb-5">Hasil sudah masuk ke panitia. Terima kasih!</p>

          <div class="grid grid-cols-3 gap-3 max-w-sm mx-auto mb-5">
            <div class="bg-navy-soft rounded-xl p-3">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Skor</p>
              <p class="text-2xl font-extrabold text-navy">{{ $ujian->skor }}</p>
            </div>
            <div class="bg-navy-soft rounded-xl p-3">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Benar</p>
              <p class="text-2xl font-extrabold text-navy">{{ $ujian->benar }}/{{ $ujian->total }}</p>
            </div>
            <div class="bg-navy-soft rounded-xl p-3">
              <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 mb-1">Selesai</p>
              <p class="text-[13px] font-bold text-navy leading-tight mt-1.5">{{ $ujian->selesai_pada?->format('d M Y H:i') }}</p>
            </div>
          </div>

          <a href="{{ route('spmb.pendaftar.kartu') }}" class="inline-block bg-navy text-white rounded-xl px-5 py-2.5 font-semibold">Lihat Kartu Tes</a>
        </div>
      @elseif ($ujian && $ujian->status === 'sedang' && $ujian->sisa_detik > 0)
        {{-- ============ LANJUTKAN ============ --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5 mb-5">
          <p class="font-bold text-amber-900 mb-0.5">Tes Anda sedang berjalan</p>
          <p class="text-amber-800 text-[12.5px]">Sisa waktu ± {{ intdiv($ujian->sisa_detik, 60) }} menit {{ $ujian->sisa_detik % 60 }} detik. Jawaban yang belum dikirim akan hilang jika waktu habis.</p>
        </div>
        <a href="{{ route('spmb.pendaftar.kerjakan') }}" class="block text-center bg-emerald-600 text-white rounded-xl px-5 py-3 font-bold">Lanjutkan Tes Sekarang</a>
      @else
        {{-- ============ FORM KODE ============ --}}
        <p class="text-slate-600 mb-4">
          Masukkan <b>kode login tes</b> yang tertera di kartu tes Anda untuk mulai mengerjakan.
        </p>

        @if ($paket)
          <div class="bg-navy-soft rounded-xl px-4 py-3 mb-4 text-[12.5px]">
            <p class="font-bold text-navy">{{ $paket->judul }}</p>
            <p class="text-slate-600 mt-0.5">Durasi {{ $paket->durasi_menit }} menit · {{ $paket->soal()->count() }} soal pilihan ganda</p>
            <p class="text-slate-500 mt-0.5">Jadwal: {{ $paket->jadwal }}</p>
          </div>
        @else
          <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-4 text-[12.5px] text-amber-800">
            Tes belum dibuka panitia. Coba lagi sesuai jadwal di kartu tes.
          </div>
        @endif

        <form action="{{ route('spmb.pendaftar.tes.mulai') }}" method="post">
          @csrf
          <label class="block mb-1.5 font-medium text-navy">Kode tes</label>
          <input name="kode" value="{{ old('kode') }}" maxlength="12" placeholder="CONTOH: K7X2QM"
                 class="w-full border border-slate-200 rounded-xl px-3.5 py-3 font-mono text-[16px] tracking-[.3em] uppercase"
                 style="font-family:'Courier New',monospace" autocomplete="off" required>
          <button class="w-full mt-4 bg-emerald-600 text-white rounded-xl px-5 py-3 font-bold">Mulai Tes</button>
          <p class="text-[12px] text-slate-500 mt-3">Setelah tombol ditekan, waktu tes mulai berjalan. Jangan menutup halaman sampai selesai.</p>
        </form>
      @endif
    </div>

    <p class="text-center mt-4"><a href="{{ route('spmb.pendaftar.dasbor') }}" class="text-[13px] text-slate-500 underline">Kembali ke dasbor</a></p>
  </div>
</x-seksi>
@endsection
