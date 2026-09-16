@extends('layouts.publik')
@section('judul', 'Profil Saya')
@section('isi')
@include('partials.hero', ['kecil' => 'Akun', 'judul' => 'Profil Saya', 'sub' => $user->name . ' · ' . $user->peran_label])
<x-seksi judul="Data Akun">
  <div class="kartu p-6 max-w-xl text-[13.5px]">
    <table class="w-full"><tbody>
      <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500 w-40">Nama</td><td class="py-2.5 font-semibold text-navy">{{ $user->name }}</td></tr>
      <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500">Email</td><td class="py-2.5">{{ $user->email }}</td></tr>
      <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500">Peran</td><td class="py-2.5">{{ $user->peran_label }}</td></tr>
      @if ($user->alumniProfile)
        <tr class="border-b border-slate-100"><td class="py-2.5 text-slate-500">Tahun lulus</td><td class="py-2.5">{{ $user->alumniProfile->tahun_lulus ?: '-' }}</td></tr>
        <tr><td class="py-2.5 text-slate-500">Status direktori</td><td class="py-2.5">{{ $user->alumniProfile->diverifikasi_pada ? 'Terverifikasi' : 'Menunggu verifikasi admin' }}</td></tr>
      @endif
    </tbody></table>
    <form action="{{ route('logout') }}" method="post" class="mt-6">@csrf<button class="border border-slate-200 rounded-xl px-4 py-2 font-semibold text-slate-600">Keluar</button></form>
  </div>
</x-seksi>
@endsection
