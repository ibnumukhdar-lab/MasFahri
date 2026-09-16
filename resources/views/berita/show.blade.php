@extends('layouts.publik')
@section('judul', $post->judul . ' — SMA IT Arafah Boarding School')
@section('deskripsi', $post->ringkas)
@section('isi')
<article class="wrap py-12 max-w-3xl">
  <span class="chip">{{ $post->kategori_utama->nama ?? 'Berita' }}</span>
  <h1 class="text-[26px] md:text-[34px] font-extrabold text-navy leading-tight mt-3 mb-3">{{ $post->judul }}</h1>
  <p class="text-[13px] text-slate-500 mb-8">{{ $post->tanggal_indonesia }} · SMA IT Arafah</p>
  @if ($post->gambar_url)<img src="{{ $post->gambar_url }}" alt="" class="w-full rounded-2xl mb-6">@endif
  <div class="kartu p-6 md:p-8 prose-isi text-[15px] leading-[1.8] text-slate-700">{!! $post->body !!}</div>
  <div class="mt-8 text-[13px]">
    <a href="{{ route('berita') }}" class="font-semibold text-navy underline">← Kembali ke daftar berita</a>
  </div>
</article>
@endsection
