@extends('layouts.publik')
@section('judul', 'Komunitas Alumni SMAITA')
@section('isi')
@include('partials.hero', ['kecil' => 'Alumni', 'judul' => 'Komunitas Alumni SMAITA', 'sub' => 'Daftarkan diri untuk masuk direktori alumni dan tetap terhubung dengan almamater.'])
<x-seksi judul="Registrasi Alumni">
  <div class="grid md:grid-cols-[1.1fr_1fr] gap-6">
    <form action="{{ route('alumni.simpan') }}" method="post" class="kartu p-6 text-[13.5px] space-y-3">
      @csrf
      <div><label class="block mb-1.5 font-medium text-navy">Nama Lengkap</label><input name="name" value="{{ old('name') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Nomor WhatsApp</label><input name="whatsapp" value="{{ old('whatsapp') }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block mb-1.5 font-medium text-navy">Tahun Lulus</label><input name="tahun_lulus" value="{{ old('tahun_lulus') }}" placeholder="2024" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
        <div><label class="block mb-1.5 font-medium text-navy">Kelas Akhir</label><input name="kelas_akhir" value="{{ old('kelas_akhir') }}" placeholder="XII IPA 1" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      </div>
      <div><label class="block mb-1.5 font-medium text-navy">Lanjut ke</label><input name="lanjut_ke" value="{{ old('lanjut_ke') }}" placeholder="Kuliah / kerja / pesantren" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Sandi akun</label><input type="password" name="password" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <button class="bg-navy text-white rounded-xl px-5 py-2.5 font-semibold">Daftar jadi alumni</button>
      <p class="text-[12.5px] text-slate-500">Akun menunggu verifikasi admin sebelum tampil di direktori.</p>
    </form>
    <div>
      <div class="kartu p-6">
        <p class="font-bold text-navy mb-3">Direktori Alumni</p>
        <form class="flex gap-2 mb-4" action="{{ route('alumni') }}" method="get">
          <input name="q" value="{{ request('q') }}" placeholder="Cari nama / tahun lulus…" class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-[13px]">
          <button class="border border-navy-soft text-navy text-[13px] font-semibold px-4 rounded-xl">Cari</button>
        </form>
        <div class="grid grid-cols-2 gap-3 text-[12.5px] text-center">
          @forelse ($alumni as $a)
            <div class="border border-slate-200 rounded-xl p-3">
              <div class="w-12 h-12 mx-auto rounded-full bg-navy-soft mb-2 overflow-hidden">
                @if ($a->foto ?? null)<img src="{{ $a->foto }}" class="w-full h-full object-cover">@endif
              </div>
              <p class="font-semibold text-navy">{{ \Illuminate\Support\Str::limit($a->user->name ?? '', 18) }}</p>
              <p class="text-slate-500">Angkatan {{ $a->tahun_lulus ?: '-' }}</p>
            </div>
          @empty
            <p class="text-slate-500 text-left col-span-2">Belum ada alumni terverifikasi.</p>
          @endforelse
        </div>
      </div>
      <div class="kartu p-6 mt-4 text-[13.5px] text-slate-600">Sudah punya akun? <a class="text-navy font-semibold underline" href="{{ route('login') }}">Masuk di sini</a> · Guru &amp; musyrif juga masuk lewat halaman yang sama.</div>
    </div>
  </div>
</x-seksi>
@endsection
