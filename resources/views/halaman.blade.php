@extends('layouts.publik')
@section('judul', $page->meta_judul ?: $page->judul . ' — SMA IT Arafah Boarding School')
@section('deskripsi', $page->meta_deskripsi ?: \Illuminate\Support\Str::limit(strip_tags($page->body ?? ''), 155))
@section('isi')
@include('partials.hero', ['kecil' => $kecil ?? 'Profil', 'judul' => $page->judul, 'sub' => $sub ?? null])
<x-seksi :judul="$page->judul">
  <div class="kartu p-6 md:p-8 prose-isi text-[14.5px] leading-relaxed text-slate-700">
    {!! $page->body ?: '<p>Isi halaman ini belum diisi.</p>' !!}
  </div>
</x-seksi>
@endsection
