<x-filament-panels::page>
<div class="rp">
<style>
.rp{--rp-garis:#e6ebf2;--rp-teks:#0f172a;--rp-redup:#64748b;--rp-lembut:#94a3b8;--rp-latar:#ffffff;--rp-kepala:#f8fafc;--rp-hijau:#15803d;--rp-merah:#b91c1c;font-size:13px;color:var(--rp-teks)}
.rp *{box-sizing:border-box}
.rp-kepala{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;justify-content:space-between;margin-bottom:14px}
.rp-judul{margin:0;font-size:19px;font-weight:800;letter-spacing:-.01em;color:var(--rp-teks)}
.rp-sub{margin:4px 0 0;font-size:12.5px;line-height:1.5;color:var(--rp-redup);max-width:640px}
.rp-cari{width:250px;max-width:100%;padding:8px 12px;border:1px solid var(--rp-garis);border-radius:10px;font-size:13px;background:var(--rp-latar);color:var(--rp-teks);outline:none}
.rp-cari:focus{border-color:#1f3a5f;box-shadow:0 0 0 3px rgba(31,58,95,.10)}
.rp-ringkas{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:16px}
.rp-stat{border:1px solid var(--rp-garis);border-radius:12px;padding:10px 12px;background:var(--rp-latar)}
.rp-stat-angka{display:block;font-size:20px;font-weight:800;line-height:1.2;color:var(--rp-teks)}
.rp-stat-label{display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--rp-lembut);margin-top:2px}
.rp-tabel-bungkus{border:1px solid var(--rp-garis);border-radius:14px;background:var(--rp-latar);overflow:auto;max-width:100%}
.rp-tabel{width:100%;min-width:940px;border-collapse:collapse;font-size:13px}
.rp-tabel thead th{position:sticky;top:0;z-index:1;background:var(--rp-kepala);text-align:left;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:var(--rp-redup);padding:9px 8px;border-bottom:1px solid var(--rp-garis);white-space:nowrap}
.rp-tabel thead th.rp-tengah{text-align:center}
.rp-tabel thead th.rp-kanan{text-align:right}
.rp-tabel thead th small{display:block;font-size:9.5px;font-weight:600;text-transform:none;letter-spacing:0;color:var(--rp-lembut)}
.rp-tabel tbody td{padding:9px 8px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.rp-tabel tbody tr:last-child td{border-bottom:0}
.rp-tabel tbody tr:hover{background:#fbfdff}
.rp-nama{display:block;font-weight:700;color:var(--rp-teks);line-height:1.25}
.rp-meta{display:block;font-size:11px;color:var(--rp-lembut);margin-top:2px;font-variant-numeric:tabular-nums}
.rp-no{color:var(--rp-lembut);font-weight:600}
.rp-num{text-align:center;font-weight:700;font-variant-numeric:tabular-nums}
.rp-num.kurang{color:var(--rp-merah)}
.rp-num.kosong{color:#c3ccd8;font-weight:500}
.rp-rata{text-align:center;font-weight:800;font-variant-numeric:tabular-nums}
.rp-rata small{display:block;font-size:10px;font-weight:600;color:#b45309}
.rp-input{width:58px;padding:5px 4px;border:1px solid #cfd8e3;border-radius:8px;text-align:center;font-size:13px;font-variant-numeric:tabular-nums;background:var(--rp-latar);color:var(--rp-teks);outline:none}
.rp-input:focus{border-color:#1f3a5f;box-shadow:0 0 0 3px rgba(31,58,95,.10)}
.rp-badge{display:inline-block;padding:3px 9px;border-radius:999px;font-size:10.5px;font-weight:800;white-space:nowrap;letter-spacing:.01em}
.rp-badge.tes{background:#eef2f7;color:#41546b}
.rp-badge.diterima{background:#e7f6ec;color:#166534}
.rp-badge.ditolak{background:#fdecec;color:#991b1b}
.rp-aksi{text-align:right;white-space:nowrap}
.rp-btn{display:inline-block;padding:6px 10px;border-radius:8px;font-size:11.5px;font-weight:700;border:1px solid transparent;cursor:pointer;line-height:1.2}
.rp-btn+.rp-btn{margin-left:4px}
.rp-btn.terima{background:var(--rp-hijau);color:#fff}
.rp-btn.terima:hover{background:#166534}
.rp-btn.tolak{background:#fff;color:var(--rp-merah);border-color:#f0c9c9}
.rp-btn.tolak:hover{background:#fdf2f2}
.rp-btn.batal{background:#fff;color:#475569;border-color:#cfd8e3}
.rp-btn.batal:hover{background:#f6f8fb}
.rp-kosong{padding:28px 12px;text-align:center;color:var(--rp-redup);font-size:13px}
.rp-catatan{margin:12px 0 0;font-size:11.5px;line-height:1.6;color:var(--rp-lembut)}

/* kartu HP */
.rp-hp{display:none}
.rp-kartu{border:1px solid var(--rp-garis);border-radius:14px;background:var(--rp-latar);padding:12px;margin-bottom:10px}
.rp-kartu-atas{display:flex;gap:8px;align-items:flex-start;justify-content:space-between}
.rp-kartu-nama{font-size:14px;font-weight:800;line-height:1.25;color:var(--rp-teks)}
.rp-kartu-nomor{font-size:11px;color:var(--rp-lembut);margin-top:2px;font-variant-numeric:tabular-nums}
.rp-nilai-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px;margin-top:10px}
.rp-nilai{border:1px solid var(--rp-garis);border-radius:10px;padding:6px 4px;text-align:center;background:#fbfcfe}
.rp-nilai-label{display:block;font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;color:var(--rp-lembut);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rp-nilai-angka{font-size:15px;font-weight:800;font-variant-numeric:tabular-nums}
.rp-nilai-angka.kurang{color:var(--rp-merah)}
.rp-btq{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:8px}
.rp-btq-item{display:flex;align-items:center;justify-content:space-between;gap:6px;border:1px solid var(--rp-garis);border-radius:10px;padding:6px 8px}
.rp-btq-label{font-size:11px;font-weight:700;color:var(--rp-redup)}
.rp-kartu-aksi{display:flex;gap:6px;margin-top:10px}
.rp-kartu-aksi .rp-btn{flex:1;text-align:center;padding:8px 6px}

@media (max-width:900px){
  .rp-ringkas{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:767px){
  .rp-cari{width:100%}
  .rp-tabel-bungkus{display:none}
  .rp-hp{display:block}
  .rp-kepala{gap:10px}
  .rp-judul{font-size:17px}
}
.dark .rp{--rp-garis:#26303d;--rp-teks:#e5e9f0;--rp-redup:#93a1b3;--rp-lembut:#738294;--rp-latar:#0f141a;--rp-kepala:#151b23}
.dark .rp-tabel tbody tr:hover{background:#141b23}
.dark .rp-input,.dark .rp-cari{background:#0f141a;color:#e5e9f0;border-color:#2c3745}
.dark .rp-nilai{background:#141b23}
.dark .rp-badge.tes{background:#1d2733;color:#c3d0e0}
.dark .rp-btn.tolak{background:transparent}
.dark .rp-btn.batal{background:transparent;color:#c3d0e0}
</style>

<div class="rp-kepala">
  <div>
    <p class="rp-sub">Nilai CBT terisi otomatis dari pengerjaan siswa. Nilai BTQ (baca tulis Al-Qur'an) diisi panitia.
      Keputusan diterima / tidak diterima sepenuhnya wewenang panitia.</p>
  </div>
  <input class="rp-cari" type="search" wire:model.live.debounce.400ms="cari"
         placeholder="Cari nama / nomor pendaftaran…" autocomplete="off">
</div>

<div class="rp-ringkas">
  <div class="rp-stat"><span class="rp-stat-angka">{{ $baris->count() }}</span><span class="rp-stat-label">Pendaftar</span></div>
  <div class="rp-stat"><span class="rp-stat-angka">{{ $jumlahLengkap }}</span><span class="rp-stat-label">CBT lengkap</span></div>
  <div class="rp-stat"><span class="rp-stat-angka" style="color:var(--rp-hijau)">{{ $jumlahDiterima }}</span><span class="rp-stat-label">Diterima</span></div>
  <div class="rp-stat"><span class="rp-stat-angka" style="color:var(--rp-merah)">{{ $jumlahDitolak }}</span><span class="rp-stat-label">Tidak diterima</span></div>
</div>

{{-- ============ TAMPILAN DESKTOP ============ --}}
<div class="rp-tabel-bungkus">
  <table class="rp-tabel">
    <thead>
      <tr>
        <th style="width:38px">No</th>
        <th>Nama siswa</th>
        @foreach ($paket as $p)
          <th class="rp-tengah">
            {{ $p->mata_pelajaran ?: $p->judul }}
            @if ($p->kkm)<small>KKM {{ $p->kkm }}</small>@endif
          </th>
        @endforeach
        <th class="rp-tengah">Rata CBT</th>
        <th class="rp-tengah">BTQ baca</th>
        <th class="rp-tengah">BTQ tulis</th>
        <th class="rp-tengah">Status</th>
        <th class="rp-kanan">Keputusan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($baris as $i => $b)
        @php $r = $b['registrasi']; @endphp
        <tr>
          <td class="rp-no">{{ $i + 1 }}</td>
          <td>
            <span class="rp-nama">{{ $r->nama_siswa }}</span>
            <span class="rp-meta">{{ $r->nomor }} · {{ $r->tahun_ajaran }}</span>
          </td>

          @foreach ($paket as $p)
            @php
              $n = $b['nilai'][$p->id] ?? null;
              $bawah = $n !== null && $p->kkm && $n < $p->kkm;
            @endphp
            <td class="rp-num {{ $n === null ? 'kosong' : ($bawah ? 'kurang' : '') }}">{{ $n ?? '—' }}</td>
          @endforeach

          <td class="rp-rata">
            {{ $b['rata'] ?? '—' }}
            @if (! $b['lengkap'])<small>{{ $b['dikerjakan'] }}/{{ $paket->count() }} paket</small>@endif
          </td>

          <td class="rp-num">
            <input class="rp-input" type="number" min="0" max="100" inputmode="numeric"
                   value="{{ $b['baca'] }}" placeholder="—"
                   wire:change="simpanBtq({{ $r->id }}, 'nilai_baca', $event.target.value)">
          </td>
          <td class="rp-num">
            <input class="rp-input" type="number" min="0" max="100" inputmode="numeric"
                   value="{{ $b['tulis'] }}" placeholder="—"
                   wire:change="simpanBtq({{ $r->id }}, 'nilai_tulis', $event.target.value)">
          </td>

          <td class="rp-tengah">
            <span class="rp-badge {{ $r->status }}">{{ \App\Models\Registration::STATUS[$r->status] ?? $r->status }}</span>
          </td>

          <td class="rp-aksi">
            @if ($r->status !== 'diterima')
              <button type="button" class="rp-btn terima" wire:click="terima({{ $r->id }})"
                      wire:confirm="Terima {{ $r->nama_siswa }}? Status akan terlihat oleh siswa.">Terima</button>
            @endif
            @if ($r->status !== 'ditolak')
              <button type="button" class="rp-btn tolak" wire:click="tolak({{ $r->id }})"
                      wire:confirm="Tandai {{ $r->nama_siswa }} tidak diterima?">Tolak</button>
            @endif
            @if ($r->status !== 'tes')
              <button type="button" class="rp-btn batal" wire:click="batalkan({{ $r->id }})"
                      wire:confirm="Batalkan keputusan untuk {{ $r->nama_siswa }}?">Batalkan</button>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="{{ 7 + $paket->count() }}" class="rp-kosong">Tidak ada pendaftar yang cocok.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ============ TAMPILAN HP ============ --}}
<div class="rp-hp">
  @forelse ($baris as $b)
    @php $r = $b['registrasi']; @endphp
    <div class="rp-kartu">
      <div class="rp-kartu-atas">
        <div>
          <div class="rp-kartu-nama">{{ $r->nama_siswa }}</div>
          <div class="rp-kartu-nomor">{{ $r->nomor }} · {{ $r->tahun_ajaran }}</div>
        </div>
        <span class="rp-badge {{ $r->status }}">{{ \App\Models\Registration::STATUS[$r->status] ?? $r->status }}</span>
      </div>

      <div class="rp-nilai-grid">
        @foreach ($paket as $p)
          @php
            $n = $b['nilai'][$p->id] ?? null;
            $bawah = $n !== null && $p->kkm && $n < $p->kkm;
          @endphp
          <div class="rp-nilai">
            <span class="rp-nilai-label">{{ $p->mata_pelajaran ?: $p->judul }}</span>
            <span class="rp-nilai-angka {{ $bawah ? 'kurang' : '' }}">{{ $n ?? '—' }}</span>
          </div>
        @endforeach
        <div class="rp-nilai">
          <span class="rp-nilai-label">Rata CBT</span>
          <span class="rp-nilai-angka">{{ $b['rata'] ?? '—' }}</span>
        </div>
      </div>

      <div class="rp-btq">
        <div class="rp-btq-item">
          <span class="rp-btq-label">BTQ baca</span>
          <input class="rp-input" type="number" min="0" max="100" inputmode="numeric"
                 value="{{ $b['baca'] }}" placeholder="—"
                 wire:change="simpanBtq({{ $r->id }}, 'nilai_baca', $event.target.value)">
        </div>
        <div class="rp-btq-item">
          <span class="rp-btq-label">BTQ tulis</span>
          <input class="rp-input" type="number" min="0" max="100" inputmode="numeric"
                 value="{{ $b['tulis'] }}" placeholder="—"
                 wire:change="simpanBtq({{ $r->id }}, 'nilai_tulis', $event.target.value)">
        </div>
      </div>

      <div class="rp-kartu-aksi">
        @if ($r->status !== 'diterima')
          <button type="button" class="rp-btn terima" wire:click="terima({{ $r->id }})"
                  wire:confirm="Terima {{ $r->nama_siswa }}?">Terima</button>
        @endif
        @if ($r->status !== 'ditolak')
          <button type="button" class="rp-btn tolak" wire:click="tolak({{ $r->id }})"
                  wire:confirm="Tandai {{ $r->nama_siswa }} tidak diterima?">Tolak</button>
        @endif
        @if ($r->status !== 'tes')
          <button type="button" class="rp-btn batal" wire:click="batalkan({{ $r->id }})"
                  wire:confirm="Batalkan keputusan {{ $r->nama_siswa }}?">Batalkan</button>
        @endif
      </div>
    </div>
  @empty
    <div class="rp-kartu rp-kosong">Tidak ada pendaftar yang cocok.</div>
  @endforelse
</div>

<p class="rp-catatan">Nilai BTQ tersimpan otomatis saat kamu pindah dari kotak isian. Angka CBT diambil dari ujian yang sudah diselesaikan siswa.</p>
</div>
</x-filament-panels::page>
