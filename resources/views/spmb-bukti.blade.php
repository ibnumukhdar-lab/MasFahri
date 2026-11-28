<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bukti Kelulusan — {{ $registrasi->nomor }}</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;background:#eef2f7;font-family:'Times New Roman',Georgia,serif;color:#111}
  .bar{background:#1f3a5f;color:#fff;padding:12px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px;font-family:system-ui,sans-serif}
  .bar b{font-size:13.5px}
  .bar button{background:#fff;color:#1f3a5f;border:0;border-radius:8px;padding:8px 14px;font-weight:700;font-size:12.5px;cursor:pointer}
  .lembar{width:210mm;min-height:297mm;margin:18px auto;background:#fff;padding:18mm 20mm;box-shadow:0 10px 30px rgba(15,23,42,.12)}
  .kop{display:flex;align-items:center;gap:14px;border-bottom:3px double #111;padding-bottom:10px}
  .kop-ikon{width:64px;height:64px;border:2px solid #1f3a5f;border-radius:50%;display:grid;place-items:center;color:#1f3a5f;font-weight:800;font-size:11px;text-align:center;line-height:1.1}
  .kop-teks h1{margin:0;font-size:17px;letter-spacing:.4px}
  .kop-teks p{margin:3px 0 0;font-size:11.5px;line-height:1.5}
  h2.judul{text-align:center;margin:22px 0 2px;font-size:16px;text-decoration:underline;letter-spacing:.4px}
  .nomor{text-align:center;margin:0 0 20px;font-size:12px}
  p{font-size:12.5px;line-height:1.75;margin:8px 0}
  .baris{display:flex;gap:18px;align-items:flex-start}
  .identitas{flex:1;min-width:0}
  .identitas table{border-collapse:collapse;font-size:12.5px}
  .identitas td{padding:3px 6px;vertical-align:top}
  .identitas td:first-child{width:150px;color:#444}
  .foto{flex:none;width:32mm;height:42mm;border:1px solid #999;object-fit:cover}
  .fotokosong{display:grid;place-items:center;font-size:9px;color:#999;text-align:center;padding:4px;font-family:system-ui,sans-serif}
  table.nilai{border-collapse:collapse;width:100%;font-size:12.5px;margin:12px 0}
  table.nilai th,table.nilai td{border:1px solid #bbb;padding:6px 8px}
  table.nilai th{background:#f2f5f9;text-align:left}
  table.nilai td:last-child{text-align:right;font-weight:700}
  .lulus{margin:14px 0;padding:10px 14px;border:2px solid #14875a;color:#0f6b3f;text-align:center;font-size:15px;font-weight:800;letter-spacing:.6px;font-family:system-ui,sans-serif}
  .tanda{display:flex;justify-content:flex-end;margin-top:26px}
  .tanda div{text-align:center;font-size:12.5px;line-height:1.7}
  .tanda .nama{margin-top:54px;font-weight:700;text-decoration:underline}
  .kode{margin-top:18px;border-top:1px dashed #bbb;padding-top:8px;font-size:10.5px;color:#555;display:flex;justify-content:space-between;gap:10px;font-family:system-ui,sans-serif}
  @media print{
    body{background:#fff}
    .bar{display:none}
    .lembar{margin:0;box-shadow:none;width:auto;min-height:auto;padding:12mm 15mm}
    @page{size:A4;margin:10mm}
  }
</style>
</head>
<body>

<div class="bar">
  <b>Bukti Kelulusan · {{ $registrasi->nomor }}</b>
  <button onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="lembar">
  <div class="kop">
    <div class="kop-ikon">SMA<br>IT</div>
    <div class="kop-teks">
      <h1>SMA IT ARAFAH</h1>
      <p>Boarding School · Sampit, Kalimantan Tengah<br>
         Panitia Penerimaan Murid Baru Tahun Pelajaran {{ $registrasi->tahun_ajaran }}</p>
    </div>
  </div>

  <h2 class="judul">SURAT KETERANGAN LULUS SELEKSI</h2>
  <p class="nomor">Nomor: SPMB/{{ str_replace('SPMB-', '', $registrasi->nomor) }}/{{ str_replace('/', '-', $registrasi->tahun_ajaran) }}</p>

  <p>Yang bertanda tangan di bawah ini, Kepala SMA IT Arafah, menerangkan bahwa:</p>

  <div class="baris">
    <div class="identitas">
      <table>
        <tr><td>Nama lengkap</td><td>: <b>{{ $registrasi->nama_siswa }}</b></td></tr>
        <tr><td>Nomor pendaftaran</td><td>: {{ $registrasi->nomor }}</td></tr>
        <tr><td>Tempat, tanggal lahir</td><td>: {{ trim(($formulir->tempat_lahir ?? '').', '.($formulir->tanggal_lahir?->format('d F Y') ?? ''), ', ') ?: '—' }}</td></tr>
        <tr><td>Sekolah asal</td><td>: {{ $registrasi->sekolah_asal ?: '—' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ trim(($formulir->alamat_jalan ?? '').', '.($formulir->kelurahan ?? '').', '.($formulir->kecamatan ?? ''), ', ') ?: '—' }}</td></tr>
        <tr><td>Jalur pendaftaran</td><td>: {{ $registrasi->minat_program ?: 'Reguler' }}</td></tr>
      </table>
    </div>

    @if ($formulir && $formulir->foto_latar_merah)
      <img class="foto" src="{{ asset('media/'.$formulir->foto_latar_merah) }}" alt="Foto">
    @else
      <div class="foto fotokosong">Foto 3×4</div>
    @endif
  </div>

  <p>Berdasarkan hasil seleksi penerimaan murid baru yang meliputi Tes Potensi Akademik,
     Baca Tulis Al-Qur'an, serta Wawancara Minat dan Bakat, dengan rincian nilai sebagai berikut:</p>

  <table class="nilai">
    <tr><th>Tes Potensi Akademik</th><td>{{ $nilai['tpa'] ?? '—' }}</td></tr>
    <tr><th>Baca Tulis Al-Qur'an</th><td>{{ $nilai['btq'] ?? '—' }}</td></tr>
    <tr><th>Wawancara Minat dan Bakat</th><td>{{ $nilai['wawancara'] ?? '—' }}</td></tr>
    <tr><th>Nilai akhir</th><td>{{ $nilaiAkhir ?? '—' }}</td></tr>
  </table>

  <div class="lulus">DINYATAKAN LULUS / DITERIMA</div>

  <p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

  <div class="tanda">
    <div>
      Sampit, {{ ($tanggalPengumuman ?? now())->translatedFormat('j F Y') }}<br>
      Kepala SMA IT Arafah
      <div class="nama">(.......................................)</div>
    </div>
  </div>

  <div class="kode">
    <span>Kode verifikasi: <b>{{ $kodeVerifikasi }}</b></span>
    <span>Dicetak: {{ now()->translatedFormat('j F Y H:i') }} WIB</span>
  </div>
</div>

</body>
</html>
