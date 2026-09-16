<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('judul', 'SMA IT Arafah Boarding School')</title>
<meta name="description" content="@yield('deskripsi', 'Website resmi SMA Islam Terpadu Arafah Boarding School Sampit — Beriman, Berakhlak, Cerdas.')">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body{background:#f6f8fb;font-family:'Plus Jakarta Sans',system-ui,sans-serif}
  .wrap{max-width:1120px;margin:0 auto;padding:0 20px}
  .kartu{background:#fff;border:1px solid #e7eef6;border-radius:18px}
  .chip{display:inline-block;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;background:#eef4fb;color:#1f3a5f;border:1px solid #dbe7f4}
  .bg-navy{background:#1f3a5f} .bg-navy:hover{background:#182c47}
  .bg-navy-soft{background:#f2f6fb} .bg-navy-line{background:#c6d8ea}
  .text-navy{color:#1f3a5f} .border-navy-soft{border-color:#dbe7f4}
  .text-putih-70{color:rgba(255,255,255,.72)} .text-putih-85{color:rgba(255,255,255,.88)}
  .nav-aktif{background:#f2f6fb;color:#1f3a5f;font-weight:600}
  #menu-tutup:checked ~ .drawer{transform:translateX(0)}
  #menu-tutup:checked ~ .mask{opacity:1;pointer-events:auto}
  .drawer{transform:translateX(-102%);transition:transform .25s ease}
  .mask{opacity:0;pointer-events:none;transition:opacity .25s}
  a{text-decoration:none}
  .prose-isi p{margin:0 0 14px} .prose-isi h2{font-weight:700;margin:22px 0 10px;font-size:22px;color:#1f3a5f}
  .prose-isi h3{font-weight:700;margin:18px 0 8px;font-size:18px;color:#1f3a5f}
  .prose-isi blockquote{border-left:3px solid #c6d8ea;padding-left:14px;color:#3d5670;font-style:italic}
  .prose-isi img{border-radius:14px;margin:10px 0;max-width:100%;height:auto}
  .prose-isi ul{list-style:disc;padding-left:20px;margin:0 0 14px}
  .prose-isi ol{list-style:decimal;padding-left:20px;margin:0 0 14px}
  .prose-isi table{width:100%;border-collapse:collapse;margin:12px 0;font-size:14px}
  .prose-isi th{background:#eef2f8;color:#1f3a5f;border:1px solid #e2e9f2;padding:8px}
  .prose-isi td{border:1px solid #e2e9f2;padding:8px}
  .prose-isi .kartu{margin:0 0 12px;padding:16px}
  .prose-isi .galeri{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px}
  .prose-isi a.tombol{display:inline-block;background:#1f3a5f;color:#fff;padding:8px 16px;border-radius:12px;font-size:13px;font-weight:600;margin:4px 6px 4px 0}
</style>
</head>
<body class="text-slate-700">

<input type="checkbox" id="menu-tutup" class="hidden">
<div class="mask fixed inset-0 bg-black/40 z-40" onclick="document.getElementById('menu-tutup').checked=false"></div>
<aside class="drawer fixed top-0 left-0 h-full w-72 bg-white z-50 shadow-2xl p-5 overflow-y-auto">
  <div class="flex items-center justify-between mb-6">
    <span class="font-extrabold text-navy">SMA IT Arafah</span>
    <label for="menu-tutup" class="cursor-pointer text-slate-400 text-2xl leading-none">&times;</label>
  </div>
  <nav class="space-y-1 text-[15px]">
    @foreach (config('smaita.menu') as $label => $url)
      <a href="{{ $url }}" class="block px-3 py-2 rounded-lg hover:bg-navy-soft hover:text-navy {{ request()->is(ltrim(parse_url($url, PHP_URL_PATH) ?? '/', '/')) || ($url === url('/') && request()->is('/')) ? 'nav-aktif' : '' }}">{{ $label }}</a>
    @endforeach
  </nav>
  <a href="{{ route('spmb') }}" class="mt-6 block text-center bg-navy text-white rounded-xl py-2.5 font-semibold">Daftar Sekarang</a>
</aside>

<header class="bg-white/95 backdrop-blur border-b border-slate-200 sticky top-0 z-30">
  <div class="wrap flex items-center gap-4 h-[68px]">
    <label for="menu-tutup" class="lg:hidden cursor-pointer p-2 -ml-2 text-navy" aria-label="Menu">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </label>
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 mr-auto">
      <span class="w-9 h-9 rounded-xl bg-navy text-white grid place-items-center font-bold text-[13px]">SA</span>
      <span class="leading-tight">
        <span class="block font-extrabold text-navy text-[15px]">{{ \App\Models\Setting::ambil('nama_singkat', 'SMA IT Arafah') }}</span>
        <span class="block text-[11px] text-slate-500 -mt-0.5">Boarding School · Sampit</span>
      </span>
    </a>
    <nav class="hidden lg:flex items-center gap-1 text-[14px]">
      @foreach (array_slice(config('smaita.menu'), 0, 7, true) as $label => $url)
        <a href="{{ $url }}" class="px-3 py-2 rounded-lg hover:bg-navy-soft hover:text-navy">{{ $label }}</a>
      @endforeach
    </nav>
    <a href="{{ route('spmb') }}" class="hidden lg:inline-block bg-navy text-white text-[13px] font-semibold px-4 py-2 rounded-xl">SPMB</a>
  </div>
</header>

<main>
@if (session('sukses'))
  <div class="wrap pt-6"><div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-[13.5px]">{{ session('sukses') }}</div></div>
@endif
@yield('isi')
</main>

<footer class="bg-navy text-white mt-16">
  <div class="wrap py-12 grid gap-8 md:grid-cols-3">
    <div>
      <p class="font-extrabold mb-2">{{ \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School') }}</p>
      <p class="text-[13px] leading-relaxed text-white/85">{{ \App\Models\Setting::ambil('alamat', 'Jalan Setia Usaha No. 4, Sampit — Kabupaten Kotawaringin Timur') }}</p>
      <p class="text-[13px] mt-3 text-white/85">Berdiri 2019 · Terakreditasi A</p>
    </div>
    <div class="text-[13px]">
      <p class="font-semibold mb-3">Halaman</p>
      <div class="grid grid-cols-2 gap-y-2 text-white/90">
        <a href="{{ route('halaman', 'tentang-sma-it-arafah') }}" class="hover:text-white">Tentang</a>
        <a href="{{ route('halaman', 'kurikulum') }}" class="hover:text-white">Kurikulum</a>
        <a href="{{ route('halaman', 'tahfizh') }}" class="hover:text-white">Tahfizh</a>
        <a href="{{ route('halaman', 'diniyah') }}" class="hover:text-white">Diniyah</a>
        <a href="{{ route('galeri') }}" class="hover:text-white">Galeri</a>
        <a href="{{ route('halaman', 'ekskul') }}" class="hover:text-white">Ekskul</a>
        <a href="{{ route('berita') }}" class="hover:text-white">Berita</a>
        <a href="{{ route('alumni') }}" class="hover:text-white">Alumni</a>
      </div>
    </div>
    <div class="text-[13px]">
      <p class="font-semibold mb-3">Layanan</p>
      <div class="space-y-2 text-white/90">
        <a href="{{ route('spmb') }}" class="block hover:text-white">Pendaftaran Murid Baru (SPMB)</a>
        <a href="{{ route('kelulusan') }}" class="block hover:text-white">Pengumuman Kelulusan</a>
        <a href="{{ route('toolsguru') }}" class="block hover:text-white">Tools Guru</a>
        <a href="{{ route('login') }}" class="block hover:text-white">Masuk / Login</a>
      </div>
    </div>
  </div>
  <div class="border-t border-white/10 py-4 text-center text-[12px] text-white/75">© 2019–{{ date('Y') }} SMA IT Arafah Boarding School · Sampit</div>
</footer>
</body>
</html>
