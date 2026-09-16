@extends('layouts.publik')
@section('judul', 'Tools & Perangkat Guru — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Internal', 'judul' => 'Tools & Perangkat Guru', 'sub' => 'Kumpulan tautan wajib diketahui guru dan staf SMA IT Arafah.'])
@foreach ($tautan as $kelompok => $daftar)
  <x-seksi :judul="$kelompok">
    <div class="grid sm:grid-cols-2 gap-3">
      @foreach ($daftar as $i => $link)
        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="kartu p-4 flex items-start gap-3 hover:border-navy-soft hover:shadow-sm">
          <span class="w-8 h-8 shrink-0 rounded-lg bg-navy-soft text-navy grid place-items-center text-[12px] font-bold">{{ $i + 1 }}</span>
          <span class="text-[13.5px] font-medium text-navy leading-snug">{{ $link->judul }}<span class="block text-[12px] text-slate-500 font-normal">{{ parse_url($link->url, PHP_URL_HOST) }}</span></span>
        </a>
      @endforeach
    </div>
  </x-seksi>
@endforeach
@endsection
