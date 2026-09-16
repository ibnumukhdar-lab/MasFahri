@extends('layouts.publik')
@section('judul', 'Pengumuman Kelulusan — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Pengumuman', 'judul' => 'Pengumuman Kelulusan Kelas XII', 'sub' => 'Masukkan NISN untuk melihat status kelulusan. Data diambil langsung dari sistem sekolah.'])
<x-seksi judul="Cek Status Kelulusan">
  <form action="{{ route('kelulusan.cek') }}" method="get" class="kartu p-6 max-w-xl text-[13.5px]">
    <label class="block mb-1.5 font-medium text-navy">NISN Siswa</label>
    <div class="flex gap-2">
      <input name="nisn" value="{{ $nisn }}" placeholder="10 digit NISN" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
      <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
    </div>
  </form>
  @if ($siswa)
    <div class="kartu overflow-hidden max-w-xl mt-6">
      <div class="bg-navy-soft px-4 py-3 text-[13px] text-navy font-semibold">Hasil pencarian</div>
      <table class="w-full text-[13.5px]"><tbody>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500 w-40">Nama Lengkap</td><td class="px-4 py-2.5 font-semibold text-navy">{{ $siswa->nama }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Kelas</td><td class="px-4 py-2.5">{{ $siswa->kelas }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Status</td><td class="px-4 py-2.5">
          <span class="chip" style="{{ $siswa->status === 'lulus' ? 'background:#ecfdf5;color:#047857;border-color:#a7f3d0' : 'background:#fef2f2;color:#b91c1c;border-color:#fecaca' }}">{{ strtoupper(str_replace('_', ' ', $siswa->status)) }}</span></td></tr>
        @if ($siswa->pesan)<tr><td class="px-4 py-2.5 text-slate-500">Pesan</td><td class="px-4 py-2.5">{{ $siswa->pesan }}</td></tr>@endif
      </tbody></table>
    </div>
  @elseif (! $adaData)
    <div class="kartu p-6 max-w-xl mt-6 text-[13.5px] text-slate-600">
      Data kelulusan tahun ini <strong>belum dibuka</strong> oleh sekolah. Silakan cek kembali setelah tanggal pengumuman resmi, atau hubungi wali kelas.
    </div>
  @elseif ($nisn)
    <p class="text-rose-700 text-[14px] mt-5">NISN <strong>{{ $nisn }}</strong> tidak ditemukan pada data kelulusan. Periksa kembali angka NISN Anda atau hubungi wali kelas.</p>
  @endif
</x-seksi>
@endsection
