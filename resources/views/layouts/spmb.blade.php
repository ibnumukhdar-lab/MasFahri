<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('judul', 'Area Pendaftar SPMB — SMA IT Arafah')</title>
<meta name="description" content="Area pendaftar SPMB SMA IT Arafah Boarding School Sampit.">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="theme-color" content="#1f3a5f">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  *{box-sizing:border-box}
  body{margin:0;background:#f6f8fb;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#334155}
  a{text-decoration:none;color:inherit}
  .wrap{max-width:1120px;margin:0 auto;padding:0 20px}
  .kartu{background:#fff;border:1px solid #e7eef6;border-radius:18px}
  .chip{display:inline-block;padding:3px 11px;border-radius:999px;font-size:11px;font-weight:600;background:#eef4fb;color:#1f3a5f;border:1px solid #dbe7f4}
  .bg-navy{background:#1f3a5f} .text-navy{color:#1f3a5f} .bg-navy-soft{background:#f2f6fb} .bg-navy-line{background:#c6d8ea}
  .sf-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}

  /* ===== kerangka aplikasi ===== */
  .sp-shell{min-height:100dvh;display:flex;background:#f6f8fb}
  .sp-side{width:252px;flex:none;background:#fff;border-right:1px solid #e5e7eb;padding:16px 13px;
    position:sticky;top:0;height:100dvh;display:flex;flex-direction:column;gap:12px;overflow-y:auto}
  .sp-brand{display:flex;gap:10px;align-items:center;padding:2px 5px 6px;border-bottom:1px solid #f1f5f9;margin-bottom:2px}
  .sp-logo{width:34px;height:34px;flex:none;border-radius:10px;background:#1f3a5f;color:#fff;display:grid;place-items:center;
    font-size:10px;font-weight:800;line-height:1.05;text-align:center}
  .sp-brand b{font-size:12.5px;color:#1f3a5f;display:block;line-height:1.25}
  .sp-brand span{font-size:10.5px;color:#9ca3af}
  .sp-user{border:1px solid #e5e7eb;border-radius:12px;padding:9px 10px;background:#fafcff;display:flex;gap:9px;align-items:center}
  .sp-ava{width:32px;height:32px;flex:none;border-radius:50%;background:#eef4fb;color:#1f3a5f;display:grid;place-items:center;
    font-weight:800;font-size:11.5px}
  .sp-user b{display:block;font-size:12px;color:#0f172a;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .sp-user span{display:block;font-size:10.5px;color:#94a3b8;font-variant-numeric:tabular-nums}
  .sp-label{font-size:9.5px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#b0bac6;padding:2px 8px}
  .sp-nav{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:9px;font-size:12.5px;font-weight:600;color:#64748b;transition:background .15s,color .15s}
  .sp-nav:hover{background:#f5f9fd;color:#1f3a5f}
  .sp-nav.aktif{background:#eef4fb;color:#1f3a5f}
  .sp-nav svg{width:15px;height:15px;flex:none;opacity:.75}
  .sp-nav.aktif svg{opacity:1}
  .sp-sidefoot{margin-top:auto;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;flex-direction:column;gap:2px}
  .sp-keluar{width:100%;text-align:left;background:none;border:0;cursor:pointer;font-family:inherit}
  .sp-main-wrap{flex:1;min-width:0;display:flex;flex-direction:column}
  .sp-top{display:none;align-items:center;gap:10px;padding:9px 13px;background:#fff;border-bottom:1px solid #e5e7eb;
    position:sticky;top:0;z-index:30}
  .sp-burger{width:36px;height:36px;flex:none;border:1px solid #e5e7eb;border-radius:9px;display:grid;place-items:center;background:#fff;cursor:pointer;color:#1f3a5f}
  .sp-top-isi{min-width:0;flex:1}
  .sp-top-isi b{display:block;font-size:12.5px;color:#0f172a;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .sp-top-isi span{display:block;font-size:10.5px;color:#94a3b8}
  .sp-main{padding:22px 22px 44px;flex:1;min-width:0}
  .sp-mask{display:none}
  .sp-kaki{border-top:1px solid #e5e7eb;padding:14px 22px;font-size:11px;color:#94a3b8;background:#fff}

  @media (max-width:1023px){
    .sp-side{position:fixed;left:0;top:0;z-index:50;transform:translateX(-102%);transition:transform .22s ease;
      box-shadow:0 18px 50px rgba(15,23,42,.22);width:272px}
    .sp-side.buka{transform:translateX(0)}
    .sp-mask.tampil{display:block;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:40}
    .sp-top{display:flex}
    .sp-main{padding:16px 14px 36px}
    .sp-kaki{display:none}
  }
</style>
</head>
<body>
@php
  $r = $registrasi ?? null;
  $inisial = $r ? strtoupper(mb_substr($r->nama_siswa, 0, 1)) : 'S';
@endphp
<div class="sp-shell">

  <aside class="sp-side" id="sp-side">
    <div class="sp-brand">
      <div class="sp-logo">SMA<br>IT</div>
      <div>
        <b>SMA IT Arafah</b>
        <span>Penerimaan Murid Baru</span>
      </div>
    </div>

    @if ($r)
      <div class="sp-user">
        <div class="sp-ava">{{ $inisial }}</div>
        <div style="min-width:0">
          <b>{{ $r->nama_siswa }}</b>
          <span>{{ $r->nomor }}</span>
        </div>
      </div>
    @endif

    <div>
      <p class="sp-label">Pendaftaran</p>
      <nav style="display:flex;flex-direction:column;gap:2px">
        <a href="{{ route('spmb.pendaftar.dasbor') }}#ringkasan" class="sp-nav" data-nav="ringkasan">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
          Ringkasan
        </a>
        <a href="{{ route('spmb.pendaftar.tes') }}" class="sp-nav {{ request()->routeIs('spmb.pendaftar.tes') ? 'aktif' : '' }}" data-nav="tes">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6a2 2 0 012 2v14a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
          Tes Masuk
        </a>
        <a href="{{ route('spmb.pendaftar.dasbor') }}#hasil" class="sp-nav" data-nav="hasil">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4"/><path d="M5 4h14v5a7 7 0 01-14 0z"/><path d="M5 6H3a2 2 0 002 4h1M19 6h2a2 2 0 01-2 4h-1"/></svg>
          Hasil Seleksi
        </a>
        <a href="{{ route('spmb.pendaftar.dasbor') }}#data" class="sp-nav" data-nav="data">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"/><path d="M5 20c.6-3.8 3.4-5.8 7-5.8s6.4 2 7 5.8"/></svg>
          Data Diri
        </a>
        <a href="{{ route('spmb.pendaftar.kartu') }}" class="sp-nav {{ request()->routeIs('spmb.pendaftar.kartu') ? 'aktif' : '' }}" data-nav="kartu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M7 9h4M7 13h7M7 16h5"/><circle cx="17" cy="9" r="1.6"/></svg>
          Kartu Tes
        </a>
      </nav>
    </div>

    <div class="sp-sidefoot">
      <form action="{{ route('spmb.pendaftar.keluar') }}" method="post" style="margin:0">
        @csrf
        <button type="submit" class="sp-nav sp-keluar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 4h3a2 2 0 012 2v12a2 2 0 01-2 2h-3"/><path d="M10 8l-4 4 4 4M6 12h10"/></svg>
          Keluar
        </button>
      </form>
      <p style="font-size:10px;color:#b0bac6;padding:6px 10px 0;margin:0">© {{ date('Y') }} SMA IT Arafah</p>
    </div>
  </aside>

  <div class="sp-mask" id="sp-mask"></div>

  <div class="sp-main-wrap">
    <header class="sp-top">
      <button type="button" class="sp-burger" id="sp-burger" aria-label="Buka menu">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <div class="sp-top-isi">
        <b>{{ $r->nama_siswa ?? 'Area Pendaftar' }}</b>
        <span>{{ $r->nomor ?? 'SPMB' }}</span>
      </div>
      <div class="sp-ava">{{ $inisial }}</div>
    </header>

    <main class="sp-main">
      @yield('isi')
    </main>

    <footer class="sp-kaki">Area pendaftar SPMB SMA IT Arafah — data Anda hanya digunakan untuk proses seleksi.</footer>
  </div>
</div>

<script>
(function () {
  var side = document.getElementById('sp-side');
  var mask = document.getElementById('sp-mask');
  var btn = document.getElementById('sp-burger');
  if (!side || !btn) return;

  function tutup() { side.classList.remove('buka'); if (mask) mask.classList.remove('tampil'); }
  function buka() { side.classList.add('buka'); if (mask) mask.classList.add('tampil'); }

  btn.addEventListener('click', function () { side.classList.contains('buka') ? tutup() : buka(); });
  if (mask) mask.addEventListener('click', tutup);
  side.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', tutup); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
})();
</script>
</body>
</html>
