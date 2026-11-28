<x-filament-panels::page>
<div class="ps">
<style>
.ps{--g:#e6ebf2;--t:#0f172a;--r:#64748b;--l:#94a3b8;font-size:13px;color:var(--t)}
.ps *{box-sizing:border-box}
.ps-kartu{border:1px solid var(--g);border-radius:14px;background:#fff;padding:16px;margin-bottom:14px;max-width:720px}
.ps-judul{font-size:14px;font-weight:800;margin:0 0 2px}
.ps-sub{font-size:12px;color:var(--r);margin:0 0 12px;line-height:1.5}
.ps-label{display:block;font-size:12px;font-weight:700;color:#41546b;margin-bottom:6px}
.ps-input{padding:9px 12px;border:1px solid #cfd8e3;border-radius:10px;font-size:13.5px;outline:none;background:#fff;color:var(--t)}
.ps-input:focus{border-color:#1f3a5f;box-shadow:0 0 0 3px rgba(31,58,95,.10)}
.ps-btn{background:#1f3a5f;color:#fff;border:0;border-radius:10px;padding:9px 16px;font-size:12.5px;font-weight:700;cursor:pointer}
.ps-btn:hover{background:#16304f}
.ps-note{background:#eef4fb;border:1px solid #d6e3f1;border-radius:10px;padding:10px 12px;font-size:12px;color:#1f3a5f;line-height:1.55;margin-top:10px}
.ps-hari{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px}
.ps-hari span{background:#f2f6fb;border:1px solid #dbe7f4;border-radius:999px;padding:3px 10px;font-size:11.5px;font-weight:600;color:#1f3a5f}
.ps-kosong{color:var(--l);font-size:12px;font-style:italic}
.ps-err{background:#fdecec;border:1px solid #f3c7c7;color:#8d1c1c;border-radius:10px;padding:8px 10px;font-size:12px;margin-bottom:10px}
</style>

<div class="ps-kartu">
  <p class="ps-judul">Tanggal pengumuman hasil</p>
  <p class="ps-sub">Kosongkan bila ingin otomatis: <b>H+1 dari hari tes terakhir</b>.
    Isi manual hanya bila pengumuman sengaja dijadwalkan lain.</p>

  @if ($errors->any())
    <div class="ps-err">{{ implode(' · ', $errors->all()) }}</div>
  @endif

  <label class="ps-label" for="tgl">Tanggal pengumuman (opsional)</label>
  <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
    <input id="tgl" type="date" class="ps-input" wire:model="tanggalPengumuman">
    <button type="button" class="ps-btn" wire:click="simpan">Simpan</button>
  </div>

  <div class="ps-note">
    Otomatis dari jadwal: <b>{{ $otomatis ? $otomatis->translatedFormat('l, j F Y') : 'belum ada hari tes' }}</b><br>
    Hari tes terdaftar:
    @if ($hariTes->count())
      <div class="ps-hari">
        @foreach ($hariTes as $tgl => $daftar)
          <span>{{ \Carbon\Carbon::parse($tgl)->translatedFormat('j M Y') }} · {{ $daftar->count() }} paket</span>
        @endforeach
      </div>
    @else
      <span class="ps-kosong">belum ada paket dengan tanggal tes</span>
    @endif
  </div>
</div>

<div class="ps-kartu">
  <p class="ps-judul">Catatan</p>
  <p class="ps-sub" style="margin-bottom:0">
    Hari tes diambil dari kolom <b>Tanggal tes</b> pada menu <b>Paket Soal CBT</b>.
    Ubah tanggal di sana untuk mengatur daftar hari tes — pengumuman otomatis menyesuaikan (H+1).
  </p>
</div>
</div>
</x-filament-panels::page>
