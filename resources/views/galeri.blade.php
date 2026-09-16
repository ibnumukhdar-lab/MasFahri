@extends('layouts.publik')
@section('judul', 'Galeri Kegiatan & Prestasi — SMA IT Arafah Boarding School')
@section('isi')
@include('partials.hero', ['kecil' => 'Kesiswaan', 'judul' => 'Galeri Kegiatan & Prestasi Siswa', 'sub' => 'Dokumentasi kegiatan harian, perlombaan, dan pencapaian siswa.'])
<x-seksi judul="Album Terbaru">
  @forelse ($albums as $album)
    <div class="mb-10">
      <h3 class="font-bold text-navy text-[17px] mb-1">{{ $album->judul }}</h3>
      <p class="text-[13px] text-slate-500 mb-4">{{ $album->tanggal?->translatedFormat('d F Y') }} @if($album->keterangan) · {{ $album->keterangan }} @endif</p>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        @foreach ($album->photos as $foto)
          <a href="{{ $foto->url }}" target="_blank" class="block overflow-hidden rounded-2xl border border-slate-200">
            <img src="{{ $foto->url }}" alt="{{ $foto->judul }}" class="w-full h-44 object-cover hover:scale-105 transition duration-500" loading="lazy">
          </a>
        @endforeach
      </div>
    </div>
  @empty
    <p class="text-slate-500 text-[14px]">Belum ada album. Tambahkan lewat panel admin.</p>
  @endforelse
</x-seksi>
@endsection
