@extends('layouts.publik')
@section('judul', 'Berita & Gagasan — SMA IT Arafah Boarding School')
@section('isi')
@include('partials.hero', ['kecil' => 'Informasi', 'judul' => 'Berita, Prestasi & Gagasan', 'sub' => 'Kabar terbaru dari kegiatan dan prestasi siswa SMA IT Arafah Sampit.'])
<x-seksi judul="Semua Tulisan">
  <div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('berita') }}" class="chip {{ request('kategori') ? '' : 'nav-aktif' }}">Semua · {{ $total }}</a>
    @foreach ($kategori as $k)
      <a href="{{ route('berita', ['kategori' => $k->slug]) }}" class="chip {{ request('kategori') === $k->slug ? 'nav-aktif' : '' }}">{{ $k->nama }} · {{ $k->posts_count }}</a>
    @endforeach
  </div>
  <form class="mb-6" action="{{ route('berita') }}" method="get">
    <input name="q" value="{{ request('q') }}" placeholder="Cari tulisan…" class="w-full md:w-80 border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13.5px]">
  </form>
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($posts as $post)@include('partials.kartu-berita', ['post' => $post])@empty
      <p class="text-slate-500 text-[14px]">Belum ada tulisan yang cocok.</p>
    @endforelse
  </div>
  <div class="mt-8">{{ $posts->links() }}</div>
</x-seksi>
@endsection
