@extends('layouts.publik')
@section('judul', 'Masuk — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Akun', 'judul' => 'Masuk', 'sub' => 'Untuk guru, musyrif, ustadz diniyah, orang tua, dan alumni.'])
<x-seksi judul="Login">
  <form action="{{ route('login.proses') }}" method="post" class="kartu p-6 md:p-8 max-w-md text-[13.5px] space-y-3">
    @csrf
    @if ($errors->any())<div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-3 py-2 text-[12.5px]">{{ $errors->first() }}</div>@endif
    <div><label class="block mb-1.5 font-medium text-navy">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Sandi</label><input type="password" name="password" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <button class="w-full bg-navy text-white rounded-xl py-2.5 font-semibold">Masuk</button>
    <p class="text-slate-500">Belum punya akun? <a href="{{ route('alumni') }}" class="text-navy font-semibold underline">Daftar alumni</a></p>
  </form>
</x-seksi>
@endsection
