@extends('layouts.publik')
@section('judul', 'Status Pendaftaran — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'SPMB', 'judul' => 'Status Pendaftaran', 'sub' => 'Masukkan nomor pendaftaran Anda.'])
<x-seksi judul="Hasil">
  <form action="{{ route('spmb.status') }}" method="get" class="mb-6 flex gap-2 max-w-xl text-[13.5px]">
    <input name="nomor" value="{{ $nomor }}" placeholder="SPMB-2026-0001" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
    <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
  </form>
  @if ($registrasi)
    <div class="kartu overflow-hidden max-w-xl">
      <div class="bg-navy-soft px-4 py-3 text-[13px] text-navy font-semibold">{{ $registrasi->nomor }}</div>
      <table class="w-full text-[13.5px]"><tbody>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500 w-40">Nama siswa</td><td class="px-4 py-2.5 font-semibold text-navy">{{ $registrasi->nama_siswa }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Nama wali</td><td class="px-4 py-2.5">{{ $registrasi->nama_wali }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Tahun ajaran</td><td class="px-4 py-2.5">{{ $registrasi->tahun_ajaran }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Status</td><td class="px-4 py-2.5"><span class="chip">{{ \App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status }}</span></td></tr>
        @if ($registrasi->pesan_admin)<tr><td class="px-4 py-2.5 text-slate-500">Pesan</td><td class="px-4 py-2.5">{{ $registrasi->pesan_admin }}</td></tr>@endif
      </tbody></table>
    </div>
  @elseif ($nomor)
    <p class="text-rose-700 text-[14px]">Nomor <strong>{{ $nomor }}</strong> tidak ditemukan. Periksa kembali atau hubungi admin sekolah.</p>
  @endif
</x-seksi>
@endsection
