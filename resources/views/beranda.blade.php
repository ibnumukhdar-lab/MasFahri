@extends('layouts.publik')

@section('judul', \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Beriman, Berakhlak, Cerdas')
@section('deskripsi', 'Website resmi '.\App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Sampit, Kotawaringin Timur.')

@section('isi')
<style>
  /* ---------- beranda: bagian-bagian yang bisa diatur dari panel ---------- */
  .bg-bagian { padding: 52px 0; }
  @media (min-width: 768px) { .bg-bagian { padding: 72px 0; } }
  .bg-judul { font-size: 22px; line-height: 1.25; font-weight: 800; color: #1f3a5f; letter-spacing: -.01em; }
  @media (min-width: 768px) { .bg-judul { font-size: 30px; } }
  .bg-lead { font-size: 14.5px; line-height: 1.75; color: #55657a; margin-top: 12px; }
  @media (min-width: 768px) { .bg-lead { font-size: 15.5px; } }
  .bg-kartu { background: #fff; border: 1px solid #e7eef6; border-radius: 20px; transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
  .bg-kartu:hover { transform: translateY(-2px); box-shadow: 0 18px 40px -26px rgba(31,58,95,.5); border-color: #d6e3f2; }
  .bg-centang { width: 22px; height: 22px; border-radius: 999px; background: #eef4fb; color: #1f3a5f; display: grid; place-items: center; font-size: 12px; font-weight: 800; flex: none; margin-top: 2px; }
  .bg-angka { font-size: 30px; font-weight: 800; color: #1f3a5f; line-height: 1; }
  @media (min-width: 768px) { .bg-angka { font-size: 38px; } }
  .bg-foto { border-radius: 22px; overflow: hidden; border: 1px solid #e7eef6; background: #eef4fb; }
  .bg-foto img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s ease; }
  .bg-foto:hover img { transform: scale(1.04); }
  .bg-tanya { background: #fff; border: 1px solid #e7eef6; border-radius: 16px; padding: 16px 18px; }
  .bg-tanya summary { cursor: pointer; font-weight: 700; color: #1f3a5f; font-size: 15px; }
  .bg-tanya p { margin: 10px 0 0; font-size: 14.5px; line-height: 1.7; color: #55657a; }
  .bg-hero-tbl { display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; padding: 11px 20px; border-radius: 999px; }
  .bg-ubah { position: absolute; top: 14px; right: 14px; z-index: 20; background: #fff; color: #1f3a5f; border: 1px solid #dbe7f4;
    border-radius: 999px; padding: 6px 12px; font-size: 11.5px; font-weight: 700; box-shadow: 0 6px 18px -10px rgba(31,58,95,.5); }
  .bg-ubah:hover { background: #1f3a5f; color: #fff; }
</style>

@forelse ($bagian as $b)
  @include('beranda.bagian', ['b' => $b])
@empty
  @include('partials.hero', [
    'kecil' => 'Selamat Datang',
    'judul' => '<span class="text-white">Membentuk Generasi Modern, yang Beriman, Berakhlak dan Cerdas</span>',
    'sub' => 'Bagian beranda belum diatur. Buka panel → Konten → Halaman Beranda untuk menambah bagian.',
  ])
@endforelse
@endsection
