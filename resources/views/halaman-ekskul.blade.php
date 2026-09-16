@extends('layouts.publik')
@section('judul', 'Ekstrakurikuler — SMA IT Arafah Boarding School')
@section('isi')
@include('partials.hero', ['kecil' => 'Kesiswaan', 'judul' => 'Ekstrakurikuler', 'sub' => 'Wadah pengembangan bakat, kepemimpinan, dan kebugaran siswa di luar jam pelajaran.'])
<x-seksi judul="Daftar Ekstrakurikuler">
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($ekskul as $e)
      <article class="kartu p-5"><h3 class="font-bold text-navy text-[15px] mb-1">{{ $e['judul'] }}</h3><p class="text-[13px] text-slate-600">{{ $e['teks'] }}</p></article>
    @endforeach
  </div>
  @if ($page && $page->body)
    <div class="kartu p-6 md:p-8 prose-isi text-[14.5px] mt-6">{!! $page->body !!}</div>
  @endif
</x-seksi>
@endsection
