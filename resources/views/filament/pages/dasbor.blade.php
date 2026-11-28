@php
    use App\Models\Registration;
    use App\Models\AlumniCerita;

    $total     = Registration::count();
    $tahapTes  = Registration::where('status', 'tes')->count();
    $diterima  = Registration::where('status', 'diterima')->count();
    $hariIni   = Registration::whereDate('created_at', today())->count();
    $cerita    = AlumniCerita::count();
    $ceritaBaru= AlumniCerita::whereNull('diverifikasi_pada')->count();
    $terbaru   = Registration::orderByDesc('id')->take(6)->get(['id', 'nomor', 'nama_siswa', 'status', 'created_at']);

    $statusMap = [
        'baru' => ['Baru', '#eef4fb', '#1f3a5f'],
        'verifikasi' => ['Verifikasi', '#fff8e8', '#8a5a00'],
        'tes' => ['Tahap tes', '#fff8e8', '#8a5a00'],
        'diterima' => ['Diterima', '#e7f6ec', '#166534'],
        'ditolak' => ['Tidak diterima', '#fdecec', '#991b1b'],
    ];

    $menu = [
        ['Pendaftaran SPMB', 'Data pendaftar, verifikasi, status', \App\Filament\Resources\Registrations\RegistrationResource::getUrl(), 'M4 6h16M4 12h16M4 18h10'],
        ['Rekap Hasil Tes', 'Nilai CBT, BTQ, keputusan panitia', \App\Filament\Pages\RekapNilaiSpmb::getUrl(), 'M4 19V9m5 10V5m5 14v-7m5 7V8'],
        ['Paket Soal CBT', 'Jadwal, durasi, paket per mapel', \App\Filament\Resources\SpmbPaketSoals\SpmbPaketSoalResource::getUrl(), 'M8 4h8a2 2 0 012 2v14l-6-3-6 3V6a2 2 0 012-2z'],
        ['Bank Soal', 'Kumpulan soal siap pakai', \App\Filament\Resources\SpmbBankSoals\SpmbBankSoalResource::getUrl(), 'M4 5h16v14H4zM8 9h8M8 13h5'],
        ['Cerita Alumni', 'Verifikasi dan terbitkan cerita', \App\Filament\Resources\AlumniCeritas\AlumniCeritaResource::getUrl(), 'M12 3l2.6 5.6 6.1.8-4.5 4.2 1.1 6-5.3-3-5.3 3 1.1-6L3.3 9.4l6.1-.8z'],
        ['Pengaturan SPMB', 'Tanggal pengumuman & hari tes', \App\Filament\Pages\PengaturanSpmb::getUrl(), 'M12 15a3 3 0 100-6 3 3 0 000 6zM19 12a7 7 0 00-.1-1l2-1.5-2-3.4-2.3 1a7 7 0 00-1.7-1L15 3H9l-.6 2.1a7 7 0 00-1.7 1l-2.3-1-2 3.4L4.1 11a7 7 0 000 2l-1.7 1.5 2 3.4 2.3-1a7 7 0 001.7 1L9 21h6l.6-2.1a7 7 0 001.7-1l2.3 1 2-3.4L19.9 13c.1-.3.1-.7.1-1z'],
    ];
@endphp

<style>
  .db-page{display:flex;flex-direction:column;gap:16px;max-width:1180px}
  .db-halo{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:10px}
  .db-halo h1{margin:0;font-size:22px;font-weight:700;color:#14273d;letter-spacing:-.01em}
  .db-halo p{margin:4px 0 0;font-size:13px;color:#8494a7}
  .db-tgl{font-size:11.5px;font-weight:600;color:#5b7691;background:#fff;border:1px solid #e6edf5;border-radius:999px;padding:6px 12px}

  .db-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
  .db-stat{background:#fff;border:1px solid #e6edf5;border-radius:14px;padding:14px 16px;box-shadow:0 1px 2px rgba(16,24,40,.04);position:relative;overflow:hidden}
  .db-stat:before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:#1f3a5f;opacity:.85}
  .db-stat.ok:before{background:#1f9d61}
  .db-stat.warn:before{background:#d9a300}
  .db-stat b{display:block;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#8494a7}
  .db-stat strong{display:block;font-size:26px;line-height:1.15;font-weight:700;color:#14273d;margin-top:6px;font-variant-numeric:tabular-nums}
  .db-stat span{display:block;font-size:11.5px;color:#8494a7;margin-top:2px}

  .db-grid{display:grid;grid-template-columns:1.35fr 1fr;gap:12px;align-items:start}
  .db-kartu{background:#fff;border:1px solid #e6edf5;border-radius:14px;box-shadow:0 1px 2px rgba(16,24,40,.04);overflow:hidden}
  .db-kepala{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:13px 16px;border-bottom:1px solid #eef3f9}
  .db-kepala h2{margin:0;font-size:13.5px;font-weight:700;color:#243b53}
  .db-kepala a{font-size:11.5px;font-weight:600;color:#1f3a5f;text-decoration:none}

  .db-tile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;padding:12px}
  .db-tile{display:flex;gap:10px;align-items:flex-start;padding:11px 12px;border:1px solid #edf2f8;border-radius:11px;background:#fbfdff;transition:border-color .15s,background .15s,transform .15s}
  .db-tile:hover{border-color:#cddff0;background:#f5faff;transform:translateY(-1px)}
  .db-ikon{width:30px;height:30px;flex:none;border-radius:9px;background:#eef4fb;color:#1f3a5f;display:grid;place-items:center}
  .db-ikon svg{width:15px;height:15px}
  .db-tile b{display:block;font-size:12.5px;font-weight:700;color:#243b53;line-height:1.25}
  .db-tile span{display:block;font-size:11px;color:#8494a7;margin-top:2px;line-height:1.35}

  .db-list{display:flex;flex-direction:column}
  .db-row{display:flex;align-items:center;gap:10px;padding:9px 16px;border-top:1px solid #f3f6fa}
  .db-row:first-child{border-top:0}
  .db-ava{width:28px;height:28px;flex:none;border-radius:50%;background:#eef4fb;color:#1f3a5f;display:grid;place-items:center;font-size:11px;font-weight:700}
  .db-row-isi{min-width:0;flex:1}
  .db-row-isi b{display:block;font-size:12.5px;font-weight:600;color:#243b53;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .db-row-isi span{display:block;font-size:10.5px;color:#93a4b8;font-variant-numeric:tabular-nums}
  .db-badge{font-size:10.5px;font-weight:700;padding:3px 9px;border-radius:999px;white-space:nowrap}
  .db-kosong{padding:22px 16px;text-align:center;font-size:12px;color:#93a4b8}

  @media(max-width:1200px){.db-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:900px){.db-grid{grid-template-columns:1fr}}
  @media(max-width:560px){
    .db-halo h1{font-size:19px}
    .db-tile-grid{grid-template-columns:1fr}
    .db-stat strong{font-size:23px}
  }
</style>

<div class="db-page">
  <div class="db-halo">
    <div>
      <h1>Assalamu'alaikum, {{ auth()->user()?->name }}</h1>
      <p>Ringkasan penerimaan murid baru dan konten situs SMA IT Arafah.</p>
    </div>
    <div class="db-tgl">{{ now()->translatedFormat('l, j F Y') }}</div>
  </div>

  <div class="db-stat-grid">
    <div class="db-stat">
      <b>Total pendaftar</b>
      <strong>{{ number_format($total, 0, ',', '.') }}</strong>
      <span>{{ $hariIni }} masuk hari ini</span>
    </div>
    <div class="db-stat warn">
      <b>Tahap tes</b>
      <strong>{{ number_format($tahapTes, 0, ',', '.') }}</strong>
      <span>menunggu hasil & keputusan</span>
    </div>
    <div class="db-stat ok">
      <b>Diterima</b>
      <strong>{{ number_format($diterima, 0, ',', '.') }}</strong>
      <span>sudah diputuskan panitia</span>
    </div>
    <div class="db-stat {{ $ceritaBaru > 0 ? 'warn' : '' }}">
      <b>Cerita alumni</b>
      <strong>{{ number_format($cerita, 0, ',', '.') }}</strong>
      <span>{{ $ceritaBaru }} menunggu verifikasi</span>
    </div>
  </div>

  <div class="db-grid">
    <div class="db-kartu">
      <div class="db-kepala">
        <h2>Menu cepat</h2>
        <a href="{{ \App\Filament\Resources\Registrations\RegistrationResource::getUrl() }}">Semua pendaftar →</a>
      </div>
      <div class="db-tile-grid">
        @foreach ($menu as [$judulMenu, $ketMenu, $tautan, $pathIkon])
          <a href="{{ $tautan }}" class="db-tile">
            <span class="db-ikon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $pathIkon }}"/></svg>
            </span>
            <span style="min-width:0">
              <b>{{ $judulMenu }}</b>
              <span>{{ $ketMenu }}</span>
            </span>
          </a>
        @endforeach
      </div>
    </div>

    <div class="db-kartu">
      <div class="db-kepala">
        <h2>Pendaftar terbaru</h2>
        <a href="{{ \App\Filament\Resources\Registrations\RegistrationResource::getUrl() }}">Lihat</a>
      </div>
      <div class="db-list">
        @forelse ($terbaru as $r)
          @php [$labelStatus, $bgStatus, $fgStatus] = $statusMap[$r->status] ?? [ucfirst($r->status), '#eef4fb', '#1f3a5f']; @endphp
          <div class="db-row">
            <span class="db-ava">{{ strtoupper(mb_substr($r->nama_siswa, 0, 1)) }}</span>
            <div class="db-row-isi">
              <b>{{ $r->nama_siswa }}</b>
              <span>{{ $r->nomor }} · {{ $r->created_at?->format('d M Y') }}</span>
            </div>
            <span class="db-badge" style="background:{{ $bgStatus }};color:{{ $fgStatus }}">{{ $labelStatus }}</span>
          </div>
        @empty
          <p class="db-kosong">Belum ada pendaftar.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
