@extends('layouts.spmb')
@section('judul', 'Kartu Tes SPMB — SMA IT Arafah')
@section('isi')
<style>
  @media print {
    header, footer, .cetak-sembunyi { display: none !important; }
    body { background: #fff; }
    .kartu-tes { box-shadow: none !important; border: 2px solid #1f3a5f !important; }
    main { padding: 0 !important; }
  }
  .kode-tes { font-family: 'Courier New', monospace; letter-spacing: .35em; }
</style>

<x-seksi judul="Kartu Tes SPMB {{ $registrasi->tahun_ajaran }}">
  <div class="kartu kartu-tes p-6 md:p-8 max-w-2xl mx-auto" style="border:2px solid #1f3a5f">
    <div class="flex items-center gap-4 pb-5 mb-5 border-b border-slate-200">
      <img src="/media/logo/logo-96.png" alt="Logo SMA IT Arafah" width="56" height="56" style="width:56px;height:56px" class="object-contain">
      <div class="leading-tight">
        <p class="font-extrabold text-navy text-[16px]">SMA IT ARAFAH BOARDING SCHOOL</p>
        <p class="text-[12px] text-slate-500">Kartu Tes Penerimaan Murid Baru · Tahun Ajaran {{ $registrasi->tahun_ajaran }}</p>
      </div>
    </div>

    <table class="w-full text-[13.5px] mb-5"><tbody>
      <tr class="border-b border-slate-100"><td class="py-2.5 pr-3 text-slate-500 w-28 md:w-48 md:pr-4">Nama siswa</td><td class="py-2.5 font-bold text-navy">{{ $registrasi->nama_siswa }}</td></tr>
      <tr class="border-b border-slate-100"><td class="py-2.5 pr-4 text-slate-500">Nomor pendaftaran</td><td class="py-2.5 font-semibold">{{ $registrasi->nomor }}</td></tr>
      <tr class="border-b border-slate-100"><td class="py-2.5 pr-4 text-slate-500">Nama wali</td><td class="py-2.5">{{ $registrasi->nama_wali }}</td></tr>
      <tr class="border-b border-slate-100"><td class="py-2.5 pr-4 text-slate-500">Jalur</td><td class="py-2.5">{{ $registrasi->minat_program ?: '-' }}</td></tr>
      <tr class="align-top"><td class="py-2.5 pr-4 text-slate-500">Tahap tes</td><td class="py-2.5">
        <ol class="space-y-1 list-none m-0 p-0">
          <li><b>1.</b> Tes Potensi Akademik <span class="text-slate-500">(CBT, dikerjakan daring)</span></li>
          <li><b>2.</b> Baca Tulis Al-Qur'an <span class="text-slate-500">(praktik bersama penguji)</span></li>
          <li><b>3.</b> Wawancara minat dan bakat <span class="text-slate-500">(bersama pendidik)</span></li>
        </ol>
      </td></tr>
      <tr class="align-top"><td class="py-2.5 pr-4 text-slate-500">Tanggal tes</td><td class="py-2.5">
        @php $tanggalTes = $paketList->pluck('tanggal_tes')->filter()->sort()->last(); @endphp
        @if ($tanggalTes)
          <b>{{ $tanggalTes->translatedFormat('l, j F Y') }}</b>
        @else
          <span class="text-slate-500">Sesuai informasi panitia</span>
        @endif
      </td></tr>
    </tbody></table>

    <div class="bg-navy rounded-2xl px-6 py-5 text-center mb-5">
      <p class="text-[11px] font-bold uppercase tracking-widest text-putih-70 mb-1.5">Kode Login Tes (CBT)</p>
      <p class="kode-tes text-white text-[30px] md:text-[36px] font-bold">{{ $kode }}</p>
      <p class="text-[11.5px] text-putih-70 mt-2">Simpan kode ini — jangan bagikan ke orang lain.</p>
    </div>

    <div class="text-[12.5px] text-slate-600 leading-relaxed space-y-1.5">
      <p><b>Cara mengikuti tes:</b></p>
      <p>1. Masuk ke <b>Area Pendaftar</b> di smaitarafah.sch.id/spmb/masuk dengan nomor pendaftaran + nomor WhatsApp Anda.</p>
      <p>2. Buka menu <b>Tes CBT</b>, masukkan kode di atas, lalu kerjakan soal sampai selesai.</p>
      <p>3. Tes hanya bisa dikerjakan <b>satu kali</b> dan waktunya terbatas — pastikan koneksi stabil.</p>
      <p class="text-slate-500">Bawa kartu ini (cetak/simpan) saat hadir tes dan tunjukkan ke panitia.</p>
    </div>
  </div>

  <div class="cetak-sembunyi flex flex-wrap gap-3 justify-center mt-6">
    <button onclick="window.print()" class="bg-navy text-white rounded-xl px-6 py-3 font-bold text-[13.5px]">Cetak / Simpan PDF</button>
    <a href="{{ route('spmb.pendaftar.tes') }}" class="bg-emerald-600 text-white rounded-xl px-6 py-3 font-bold text-[13.5px]">Buka Tes CBT</a>
    <a href="{{ route('spmb.pendaftar.dasbor') }}" class="border border-slate-300 text-slate-600 rounded-xl px-6 py-3 font-semibold text-[13.5px]">Kembali ke Dasbor</a>
  </div>
</x-seksi>
@endsection
