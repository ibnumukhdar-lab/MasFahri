@extends('layouts.publik')
@section('judul', 'SPMB — Penerimaan Murid Baru SMA IT Arafah')
@section('isi')
<style>
  .sp-band{padding:56px 0}
  @media(min-width:768px){.sp-band{padding:72px 0}}
  .sp-kicker{display:inline-flex;align-items:center;gap:9px;font-size:11.5px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:#b98a00;margin:0 0 10px}
  .sp-kicker::before{content:"";width:26px;height:3px;border-radius:3px;background:#fdd100}
  .sp-h2{font-size:25px;line-height:1.15;font-weight:800;color:#1f3a5f;letter-spacing:-.02em;margin:0 0 8px}
  @media(min-width:768px){.sp-h2{font-size:31px}}
  .sp-lead{font-size:14px;line-height:1.75;color:#5b6470;max-width:780px}
  @media(min-width:768px){.sp-lead{font-size:15.5px}}
  .sp-h2-baris{margin-bottom:34px}
  .sp-ikon{flex:none;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;font-size:21px}
  .sp-chip-tgl{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.22);backdrop-filter:blur(4px);border-radius:999px;padding:8px 15px;font-size:12.5px;font-weight:700;color:#fff}
  .sp-chip-tgl b{color:#fdd100}
  .sp-harga{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:9px 12px;border-radius:11px;background:#f4f8fd;font-size:13px;color:#3d4a5c}
  .sp-harga b{color:#1f3a5f;font-weight:800;white-space:nowrap}
  .sp-jalur-kepala{border-radius:17px 17px 0 0;background:#1f3a5f;color:#fff;padding:13px 18px;display:flex;align-items:center;justify-content:space-between;gap:8px}
  .sp-jalur-kepala b{font-size:15px;font-weight:800}
  .sp-jalur-chip{flex:none;font-size:9.5px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;background:#fdd100;color:#1f3a5f;border-radius:999px;padding:4px 9px}
  .sp-jalur-chip--polos{background:rgba(255,255,255,.16);color:rgba(255,255,255,.85)}
  .sp-titik{flex:none;width:34px;height:34px;border-radius:999px;background:#1f3a5f;color:#fdd100;font-weight:800;font-size:14px;display:grid;place-items:center}
  .sp-ceklis{flex:none;width:22px;height:22px;border-radius:999px;background:#fdd100;display:grid;place-items:center;margin-top:1px}
  .sp-hero-pattern{background-image:radial-gradient(rgba(255,255,255,.13) 1px,transparent 1.4px);background-size:23px 23px}
</style>

{{-- ================= HERO ================= --}}
<section class="sp-hero-pattern" style="position:relative;overflow:hidden;background:linear-gradient(138deg,#24457a 0%,#1f3a5f 46%,#152847 100%)">
  <div aria-hidden="true" style="position:absolute;inset:0;background:radial-gradient(90% 130% at 85% 8%, rgba(253,209,0,.16) 0%, rgba(253,209,0,0) 55%)"></div>
  <div aria-hidden="true" style="position:absolute;width:340px;height:340px;border-radius:999px;border:1px solid rgba(255,255,255,.14);right:-110px;top:-130px"></div>
  <div aria-hidden="true" style="position:absolute;width:200px;height:200px;border-radius:999px;border:1px solid rgba(255,255,255,.10);right:60px;bottom:-110px"></div>
  <div class="wrap" style="position:relative;padding-top:64px;padding-bottom:64px">
    <p class="sp-kicker" style="color:#fdd100">Penerimaan Murid Baru {{ $tahunAjaran }}</p>
    <h1 style="font-size:31px;line-height:1.12;font-weight:800;color:#fff;letter-spacing:-.02em;max-width:22ch;margin:0 0 16px">
      @if ($gelombangAktif)
        SPMB {{ $gelombangAktif->nama }}<br>{{ $gelombangAktif->sedangBerjalan() ? 'Telah Dibuka' : ($gelombangAktif->belumDibuka() ? 'Segera Dibuka' : 'Pendaftaran Ditutup') }}
      @else
        SPMB Gelombang 1<br>Telah Dibuka
      @endif
    </h1>
    <p class="text-putih-85" style="font-size:14.5px;line-height:1.7;max-width:62ch;margin:0 0 26px">
      Kesempatan pertama masuk SMA IT Arafah tahun ajaran {{ $tahunAjaran }} — sekolah Islam terpadu boarding yang membina iman, hafalan Al-Qur'an, dan prestasi akademik sekaligus.
    </p>
    <div class="flex flex-wrap gap-2.5 mb-8">
      @php $tesPertama = $gelombangAktif?->jadwalTes->first(); @endphp
      <span class="sp-chip-tgl">🗓️ Daftar: <b>{{ $gelombangAktif?->rentang_label ?? '1 Des 2026 s/d Feb 2027' }}</b></span>
      <span class="sp-chip-tgl">📝 Tes pertama: <b>{{ $tesPertama?->waktu_label ?? 'Senin, 14 Des 2026' }}</b></span>
      <span class="sp-chip-tgl">💰 Cashback Desember: <b>Rp 1.000.000</b></span>
    </div>
    <div class="flex flex-wrap gap-3">
      <a href="#formulir" style="background:#fdd100;color:#1f3a5f;font-weight:800;font-size:14.5px;border-radius:13px;padding:14px 24px;box-shadow:0 14px 30px -12px rgba(253,209,0,.55)">Isi Formulir Pendaftaran</a>
      <a href="#syarat" style="border:1px solid rgba(255,255,255,.5);color:#fff;font-weight:700;font-size:14.5px;border-radius:13px;padding:14px 24px">Lihat Syarat &amp; Materi Tes</a>
    </div>
    <div class="flex flex-wrap gap-x-6 gap-y-2 mt-9 text-[12px] font-semibold uppercase tracking-wider" style="color:rgba(255,255,255,.62)">
      <span>✓ Terakreditasi A</span><span>✓ Boarding School</span><span>✓ Tahfizh &amp; Diniyah</span>
    </div>
  </div>
</section>

{{-- ================= JADWAL (dari data gelombang di panel) ================= --}}
<section class="sp-band" style="background:#fff">
  <div class="wrap">
    <div class="sp-h2-baris">
      <p class="sp-kicker">Jangan sampai terlewat</p>
      <h2 class="sp-h2">Jadwal SPMB {{ $tahunAjaran }}</h2>
      <p class="sp-lead">
        Pendaftaran Siswa Baru SMA IT Arafah tahun ajaran {{ $tahunAjaran }} dibuka bertahap.
        Pilih gelombang yang paling siap — setiap gelombang punya jadwal tes masuknya sendiri.
      </p>
    </div>

    @if ($gelombang->isEmpty())
      <div class="grid md:grid-cols-2 gap-4 mb-4">
        <div class="kartu p-6 flex gap-4 items-start" style="box-shadow:0 18px 40px -30px rgba(31,58,95,.45)">
          <span aria-hidden="true" class="sp-ikon" style="background:#1f3a5f">🗓️</span>
          <div>
            <p class="text-[11.5px] font-bold tracking-wide uppercase text-slate-500 mb-1.5">Pembukaan pendaftaran</p>
            <p class="font-extrabold text-navy text-[17.5px] leading-snug mb-2">1 Desember 2026 s/d Februari 2027</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">Isi formulir online kapan saja dalam periode ini.</p>
          </div>
        </div>
        <div class="kartu p-6 flex gap-4 items-start" style="box-shadow:0 18px 40px -30px rgba(31,58,95,.45)">
          <span aria-hidden="true" class="sp-ikon" style="background:#fdd100">📝</span>
          <div>
            <p class="text-[11.5px] font-bold tracking-wide uppercase text-slate-500 mb-1.5">Tes penerimaan pertama</p>
            <p class="font-extrabold text-navy text-[17.5px] leading-snug mb-2">Senin, 14 Desember 2026</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">Potensi Akademik &amp; Minat Bakat.</p>
          </div>
        </div>
      </div>
    @else
      <div class="grid md:grid-cols-2 gap-4 mb-4">
        @foreach ($gelombang as $g)
          <div class="kartu p-6" style="box-shadow:0 18px 40px -30px rgba(31,58,95,.45)">
            <div class="flex items-start justify-between gap-3 mb-3">
              <div>
                <p class="text-[11.5px] font-bold tracking-wide uppercase text-slate-500 mb-1">{{ $g->tahun_ajaran }}</p>
                <p class="font-extrabold text-navy text-[18px] leading-snug">{{ $g->nama }}</p>
              </div>
              <span class="text-[11.5px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap {{ $g->sedangBerjalan() ? 'bg-emerald-50 text-emerald-700' : ($g->belumDibuka() ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                {{ $g->status_label }}
              </span>
            </div>

            <p class="text-slate-600 text-[13.5px] leading-relaxed mb-4">
              <span class="font-bold text-navy">Pendaftaran:</span> {{ $g->rentang_label }}
            </p>

            @if ($g->jadwalTes->isEmpty())
              <p class="text-slate-500 text-[13px]">Jadwal tes menyusul — ikuti informasi panitia.</p>
            @else
              <p class="text-[11.5px] font-bold tracking-wide uppercase text-slate-500 mb-2">Jadwal tes masuk</p>
              <ul class="space-y-2.5">
                @foreach ($g->jadwalTes as $jdw)
                  <li class="flex gap-3 items-start">
                    <span class="text-[15px] leading-none mt-0.5" aria-hidden="true">{{ $jdw->jenis === 'akademik' ? '📝' : ($jdw->jenis === 'btq' ? '📖' : ($jdw->jenis === 'wawancara' ? '🎤' : '📌')) }}</span>
                    <div>
                      <p class="font-bold text-navy text-[13.5px] leading-snug">{{ $jdw->waktu_label }}</p>
                      <p class="text-slate-600 text-[13px] leading-snug">{{ $jdw->nama }}@if ($jdw->tempat) · {{ $jdw->tempat }}@endif</p>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif

            @if ($g->keterangan)
              <p class="text-slate-500 text-[12.5px] leading-relaxed mt-4 pt-3" style="border-top:1px dashed #dbe6f2">{{ $g->keterangan }}</p>
            @endif
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>

{{-- ================= PROMO DESEMBER ================= --}}
<section class="sp-band" style="background:linear-gradient(115deg,#fdd100 0%,#ffdf6b 58%,#f7c400 100%);padding-top:44px;padding-bottom:44px">
  <div class="wrap">
    <div class="flex gap-5 items-start rounded-2xl p-6 md:p-7" style="background:rgba(255,255,255,.55);border:1px solid rgba(255,255,255,.75);box-shadow:0 22px 44px -26px rgba(120,90,0,.45)">
      <span aria-hidden="true" class="sp-ikon" style="background:#1f3a5f;font-size:23px;width:54px;height:54px">💰</span>
      <div>
        <p class="text-[11.5px] font-bold tracking-[.16em] uppercase mb-1.5" style="color:#7a5b00">Promo khusus Desember</p>
        <p class="font-extrabold text-[19px] md:text-[21px] leading-snug mb-2" style="color:#1f3a5f">Cashback Rp 1.000.000 — lunasi uang pendaftaran di bulan Desember</p>
        <p class="text-[13.5px] leading-relaxed" style="color:#3d4a5c">
          Berlaku untuk <b>semua jalur</b> dan bisa <b>menumpuk</b>: pendaftar <b>Jalur Prestasi</b> atau <b>Jalur Penghafal Al-Qur'an</b> tetap mendapat potongan jalurnya.
          Contoh: hafalan 10–30 juz hemat total <b>Rp 3.000.000</b> — potongan Rp 2 juta + cashback Rp 1 juta. Formulir pendaftaran Desember juga <b>GRATIS</b>.
        </p>
      </div>
    </div>
  </div>
</section>

{{-- ================= MATERI TES ================= --}}
<section class="sp-band" id="materi" style="background:#eef4fb">
  <div class="wrap">
    <div class="sp-h2-baris">
      <p class="sp-kicker">Persiapan menghadapi tes</p>
      <h2 class="sp-h2">Yang Diujikan di Tes Penerimaan</h2>
    </div>
    <div class="grid md:grid-cols-2 gap-4 mb-4">
      <div class="kartu p-6" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
        <div class="flex items-center gap-3 mb-3">
          <span aria-hidden="true" class="sp-ikon" style="width:42px;height:42px;font-size:17px;background:#1f3a5f">✍️</span>
          <div>
            <p class="text-[10.5px] font-bold tracking-wide uppercase text-slate-500">Tertulis</p>
            <p class="font-bold text-navy text-[15.5px] leading-tight">Potensi Akademik &amp; Minat Bakat</p>
          </div>
        </div>
        <p class="text-slate-600 text-[13.5px] leading-relaxed">Mengukur kemampuan dasar calon murid — literasi, numerasi, dan pola pikir logis — sekaligus memetakan minat dan bakatnya agar sekolah bisa membimbing pengembangannya sejak awal.</p>
      </div>
      <div class="kartu p-6" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
        <div class="flex items-center gap-3 mb-3">
          <span aria-hidden="true" class="sp-ikon" style="width:42px;height:42px;font-size:17px;background:#1f3a5f">📖</span>
          <div>
            <p class="text-[10.5px] font-bold tracking-wide uppercase text-slate-500">Praktik</p>
            <p class="font-bold text-navy text-[15.5px] leading-tight">Baca Tulis Al-Qur'an &amp; Praktik Ibadah</p>
          </div>
        </div>
        <p class="text-slate-600 text-[13.5px] leading-relaxed">Membaca dan menulis Al-Qur'an (termasuk tajwid dasar) serta praktik ibadah harian — shalat, wudhu, dan amalan lainnya — sesuai pembiasaan di sekolah Islam terpadu.</p>
      </div>
    </div>
    <div class="kartu p-6" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
      <div class="flex items-center gap-3 mb-3">
        <span aria-hidden="true" class="sp-ikon" style="width:42px;height:42px;font-size:17px;background:#fdd100">🤝</span>
        <div>
          <p class="text-[10.5px] font-bold tracking-wide uppercase text-slate-500">Wawancara</p>
          <p class="font-bold text-navy text-[15.5px] leading-tight">Kesiapan Kerja Sama Orang Tua &amp; Sekolah</p>
        </div>
      </div>
      <p class="text-slate-600 text-[13.5px] leading-relaxed">Wawancara bersama wali murid: kesiapan anak menjalani kehidupan boarding, harapan keluarga, dan komitmen orang tua menjadi <b class="text-navy">mitra pendidik</b> — mendampingi hafalan, ibadah, dan belajar anak baik di sekolah maupun di rumah.</p>
    </div>
  </div>
</section>

{{-- ================= TIGA JALUR ================= --}}
<section class="sp-band" style="background:#fff">
  <div class="wrap">
    <div class="sp-h2-baris">
      <p class="sp-kicker">Pilih jalur terbaik untuk anak Anda</p>
      <h2 class="sp-h2">Tiga Jalur Pendaftaran</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
      <div class="rounded-[18px] overflow-hidden" style="border:1px solid #e7eef6;background:#fff">
        <div class="sp-jalur-kepala" style="background:#3d5987">
          <b>Jalur Reguler</b><span class="sp-jalur-chip sp-jalur-chip--polos">Jalur umum</span>
        </div>
        <div class="p-5">
          <p class="text-slate-600 text-[13.5px] leading-relaxed mb-3">Jalur umum tanpa syarat khusus: calon murid mengikuti rangkaian tes penerimaan, hasil tes menentukan kelulusan.</p>
          <ul class="text-slate-600 text-[13px] leading-relaxed list-disc pl-5 space-y-1">
            <li>Potensi akademik &amp; minat bakat</li>
            <li>Baca tulis Al-Qur'an &amp; praktik ibadah</li>
            <li>Wawancara wali murid</li>
          </ul>
        </div>
      </div>
      <div class="rounded-[18px] overflow-hidden" style="border:1px solid #e7eef6;background:#fff;box-shadow:0 22px 44px -30px rgba(31,58,95,.5)">
        <div class="sp-jalur-kepala">
          <b>Jalur Prestasi</b><span class="sp-jalur-chip">Potongan masuk</span>
        </div>
        <div class="p-5">
          <p class="text-slate-600 text-[13.5px] leading-relaxed mb-3">Untuk calon murid berprestasi akademik/non-akademik yang terbukti — potongan sesuai tingkat juara:</p>
          <div class="space-y-1.5">
            <div class="sp-harga"><span>Juara 1 tingkat <b>kabupaten/kota</b></span><b>Rp 250.000</b></div>
            <div class="sp-harga"><span>Juara 1 tingkat <b>provinsi</b></span><b>Rp 500.000</b></div>
            <div class="sp-harga"><span>Juara 2 tingkat <b>provinsi</b></span><b>Rp 250.000</b></div>
            <div class="sp-harga" style="background:#fff7d6"><span>Juara 1 tingkat <b>nasional</b></span><b>Rp 1.000.000</b></div>
            <div class="sp-harga"><span>Juara 2 tingkat <b>nasional</b></span><b>Rp 750.000</b></div>
            <div class="sp-harga"><span>Juara 3 tingkat <b>nasional</b></span><b>Rp 500.000</b></div>
          </div>
        </div>
      </div>
      <div class="rounded-[18px] overflow-hidden" style="border:1px solid #e7eef6;background:#fff;box-shadow:0 22px 44px -30px rgba(31,58,95,.5)">
        <div class="sp-jalur-kepala">
          <b>Jalur Penghafal Al-Qur'an</b><span class="sp-jalur-chip">Potongan masuk</span>
        </div>
        <div class="p-5">
          <p class="text-slate-600 text-[13.5px] leading-relaxed mb-3">Hafalan <b>dites langsung</b> saat tes penerimaan — potongan sesuai jumlah hafalan:</p>
          <div class="space-y-1.5">
            <div class="sp-harga"><span>Hafalan <b>3–5 juz</b></span><b>Rp 1.000.000</b></div>
            <div class="sp-harga"><span>Hafalan <b>5–10 juz</b></span><b>Rp 1.500.000</b></div>
            <div class="sp-harga" style="background:#fff7d6"><span>Hafalan <b>10–30 juz</b></span><b>Rp 2.000.000</b></div>
          </div>
          <div class="mt-3.5 rounded-xl px-4 py-3 text-[12.5px] leading-relaxed" style="background:#eef4fb;border:1px dashed #c6d8ea;color:#3d4a5c">
            <b class="text-navy">Bonus hafalan 10–30 juz:</b> selain potongan uang masuk, juga mendapat <b>potongan biaya SPP bulanan</b> — rinciannya dijelaskan panitia saat siswa resmi diterima.
          </div>
        </div>
      </div>
    </div>
    <div class="rounded-xl px-5 py-4 mt-4 text-[13.5px] text-slate-700 leading-relaxed" style="background:#f2f6fb;border:1px dashed #c6d8ea;max-width:860px">
      <b class="text-navy">Wajib sertifikat:</b> pendaftar <b>Jalur Prestasi</b> dan <b>Jalur Penghafal Al-Qur'an</b> wajib menunjukkan sertifikat/piagam sebagai bukti saat verifikasi — potongan berlaku setelah bukti diperiksa panitia.
    </div>
  </div>
</section>

{{-- ================= SYARAT ADMINISTRASI ================= --}}
<section class="sp-band" id="syarat" style="background:#eef4fb">
  <div class="wrap">
    <div class="sp-h2-baris">
      <p class="sp-kicker">Siapkan berkasnya</p>
      <h2 class="sp-h2">Syarat Administrasi</h2>
    </div>
    <div class="kartu p-6 md:p-7" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
      <div class="grid md:grid-cols-2 gap-x-8 gap-y-5">
        <div class="flex gap-3.5">
          <span class="sp-ceklis"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1f3a5f" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
          <div>
            <p class="font-bold text-navy mb-0.5">Biaya formulir pendaftaran</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">Dibayarkan saat pendaftaran — <b class="text-navy">khusus Desember: GRATIS</b>.</p>
          </div>
        </div>
        <div class="flex gap-3.5">
          <span class="sp-ceklis"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1f3a5f" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
          <div>
            <p class="font-bold text-navy mb-0.5">Fotokopi (FC) Akta Kelahiran</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">Akta kelahiran calon murid.</p>
          </div>
        </div>
        <div class="flex gap-3.5">
          <span class="sp-ceklis"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1f3a5f" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
          <div>
            <p class="font-bold text-navy mb-0.5">Fotokopi Kartu Keluarga (KK)</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">KK yang masih berlaku dan memuat nama calon murid.</p>
          </div>
        </div>
        <div class="flex gap-3.5">
          <span class="sp-ceklis"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#1f3a5f" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
          <div>
            <p class="font-bold text-navy mb-0.5">Rapor, surat keterangan &amp; pas foto</p>
            <p class="text-slate-600 text-[13.5px] leading-relaxed">FC rapor terakhir <b>Semester 1 kelas 9</b>, <b>surat keterangan berkelakuan baik</b>, <b>pas foto 3×4 dua lembar</b>.</p>
          </div>
        </div>
      </div>
    </div>
    <p class="text-[12.5px] text-slate-500 mt-3.5">Berkas diserahkan saat verifikasi dan tes penerimaan di sekolah — panitia akan mengonfirmasi lewat WhatsApp setelah formulir online Anda kirim.</p>
  </div>
</section>

{{-- ================= FORMULIR ================= --}}
<section class="sp-hero-pattern" id="formulir" style="position:relative;overflow:hidden;background:linear-gradient(138deg,#24457a 0%,#1f3a5f 46%,#152847 100%)">
  <div aria-hidden="true" style="position:absolute;inset:0;background:radial-gradient(80% 120% at 12% 0%, rgba(253,209,0,.14) 0%, rgba(253,209,0,0) 52%)"></div>
  <div class="wrap" style="position:relative;padding-top:64px;padding-bottom:64px">
    <div class="mb-7">
      <p class="sp-kicker" style="color:#fdd100">Langkah terakhir</p>
      <h2 style="font-size:25px;line-height:1.15;font-weight:800;color:#fff;letter-spacing:-.02em;margin:0 0 8px">
        Formulir Pendaftaran
      </h2>
      <p class="text-putih-85" style="font-size:14px;line-height:1.7;max-width:60ch">Isi data di bawah — Anda akan menerima nomor pendaftaran untuk mengecek status kapan saja.</p>
    </div>
    @if ($errors->any())
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif
    <form action="{{ route('spmb.simpan') }}" method="post" enctype="multipart/form-data" class="kartu p-6 md:p-8 grid md:grid-cols-2 gap-4 text-[13.5px]" style="box-shadow:0 30px 60px -30px rgba(6,16,34,.65)">
      @csrf
      <div><label class="block mb-1.5 font-medium text-navy">Nama Wali</label><input name="nama_wali" value="{{ old('nama_wali') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Nama Siswa</label><input name="nama_siswa" value="{{ old('nama_siswa') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
          <option value="">— Pilih —</option><option @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option>
        </select></div>
      <div><label class="block mb-1.5 font-medium text-navy">Sekolah Asal</label><input name="sekolah_asal" value="{{ old('sekolah_asal') }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div><label class="block mb-1.5 font-medium text-navy">Jalur Pendaftaran</label>
        <select name="minat_program" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
          <option value="">— Pilih jalur —</option>
          <option value="Jalur Reguler" @selected(old('minat_program') === 'Jalur Reguler')>Jalur Reguler</option>
          <option value="Jalur Prestasi" @selected(old('minat_program') === 'Jalur Prestasi')>Jalur Prestasi</option>
          <option value="Jalur Penghafal Al-Quran" @selected(old('minat_program') === 'Jalur Penghafal Al-Quran')>Jalur Penghafal Al-Quran</option>
        </select>
        <p class="text-[12px] text-slate-500 mt-1.5">Semua murid <b>wajib boarding</b> (berasrama). Tidak ada pilihan non-boarding.</p>
      </div>
      <div><label class="block mb-1.5 font-medium text-navy">Tahun Ajaran</label>
        <select name="tahun_ajaran" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
          @foreach ($pilihanTahun as $th)
            <option value="{{ $th }}" @selected(old('tahun_ajaran', $tahunAjaran) === $th)>{{ $th }}</option>
          @endforeach
        </select>
        <p class="text-[12px] text-slate-500 mt-1.5">Pilih tahun ajaran yang dituju.</p>
      </div>
      <div class="md:col-span-2">
        <div class="rounded-xl p-5 mb-1" style="background:#fffbe8;border:1px solid #f2e3ac">
          <p class="font-bold text-navy text-[14.5px] mb-2">Transfer biaya formulir — Rp 200.000</p>
          <div class="flex flex-wrap items-center gap-x-7 gap-y-2.5 text-[13.5px]">
            <span><span class="text-slate-500">Bank</span> <b class="text-navy">BRI</b></span>
            <span><span class="text-slate-500">No. rekening</span> <b class="text-navy tracking-wide" id="sp-rek">760001007929533</b></span>
            <span><span class="text-slate-500">a.n.</span> <b class="text-navy">Retno Dwi Astuti</b></span>
            <button type="button" data-salin="#sp-rek" class="rounded-lg px-3.5 py-1.5 text-[12.5px] font-bold" style="background:#1f3a5f;color:#fff">Salin no. rekening</button>
          </div>
          <p class="text-[12px] mt-2.5" style="color:#7a5b00">Khusus pendaftaran bulan Desember biaya formulir <b>GRATIS</b> — tidak perlu transfer. Luar Desember, transfer Rp 200.000 lalu unggah buktinya di bawah.</p>
        </div>
      </div>
      <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Bukti transfer (opsional bila daftar bulan Desember)</label>
        <input type="file" name="bukti_transfer" accept="image/*,.pdf" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-600" style="background:#f8fafd">
        <p class="text-[12px] text-slate-500 mt-1.5">JPG/PNG/PDF maks 4 MB — foto/screenshot bukti transfer Rp 200.000.</p>
      </div>
      <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Nomor WhatsApp Wali</label><input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
      <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Catatan (opsional)</label><textarea name="catatan" rows="2" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">{{ old('catatan') }}</textarea></div>
      <div class="md:col-span-2 flex flex-wrap gap-3 items-center">
        <button class="bg-navy text-white rounded-xl px-7 py-3 font-bold">Kirim Pendaftaran</button>
        <span class="text-[12.5px] text-slate-500">Anda akan menerima nomor pendaftaran untuk mengecek status.</span>
      </div>
    </form>
  </div>
</section>

{{-- ================= ALUR PENDAFTARAN ================= --}}
<section class="sp-band" style="background:#fff">
  <div class="wrap">
    <div class="sp-h2-baris">
      <p class="sp-kicker">Dari formulir sampai menjadi murid</p>
      <h2 class="sp-h2">Alur Pendaftaran</h2>
    </div>
    <div class="grid md:grid-cols-5 gap-4">
      <div class="kartu p-5">
        <div class="flex items-center gap-3 mb-2.5"><span class="sp-titik">1</span><p class="font-bold text-navy text-[14.5px]">Formulir &amp; transfer</p></div>
        <p class="text-slate-600 text-[13px] leading-relaxed">Isi formulir online, transfer biaya formulir <b>Rp 200.000</b> ke <b>BRI 760001007929533</b> a.n. <b>Retno Dwi Astuti</b>, lalu unggah buktinya di formulir. <b>Desember: gratis</b>, tanpa transfer.</p>
      </div>
      <div class="kartu p-5">
        <div class="flex items-center gap-3 mb-2.5"><span class="sp-titik">2</span><p class="font-bold text-navy text-[14.5px]">Verifikasi panitia</p></div>
        <p class="text-slate-600 text-[13px] leading-relaxed">Setelah verifikasi berhasil, pendaftar menerima <b>akun akses</b> ke halaman lengkapi biodata siswa.</p>
      </div>
      <div class="kartu p-5">
        <div class="flex items-center gap-3 mb-2.5"><span class="sp-titik">3</span><p class="font-bold text-navy text-[14.5px]">Kartu tes</p></div>
        <p class="text-slate-600 text-[13px] leading-relaxed">Pendaftar menerima <b>kartu tes</b> yang wajib dibawa saat hadir — dikuasai <b>1 orang tua + 1 anak</b>.</p>
      </div>
      <div class="kartu p-5">
        <div class="flex items-center gap-3 mb-2.5"><span class="sp-titik">4</span><p class="font-bold text-navy text-[14.5px]">Pengumuman</p></div>
        <p class="text-slate-600 text-[13px] leading-relaxed">Hasil tes diumumkan lewat sistem dan WhatsApp.</p>
      </div>
      <div class="kartu p-5">
        <div class="flex items-center gap-3 mb-2.5"><span class="sp-titik">5</span><p class="font-bold text-navy text-[14.5px]">Daftar ulang</p></div>
        <p class="text-slate-600 text-[13px] leading-relaxed">Murid yang diterima melakukan <b>daftar ulang</b> untuk mengunci kursinya.</p>
      </div>
    </div>
  </div>
</section>

{{-- ================= CEK STATUS ================= --}}
<section class="sp-band" style="background:#eef4fb">
  <div class="wrap">
    <div class="sp-h2-baris mb-6">
      <p class="sp-kicker">Sudah mendaftar?</p>
      <h2 class="sp-h2">Cek Status Pendaftaran</h2>
    </div>
    <form action="{{ route('spmb.status') }}" method="get" class="kartu p-6 max-w-xl text-[13.5px]" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
      <label class="block mb-1.5 font-medium text-navy">Nomor pendaftaran</label>
      <div class="flex gap-2">
        <input name="nomor" value="{{ request('nomor') }}" placeholder="SPMB-2026-0001" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
        <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
      </div>
    </form>
  </div>
</section>

<script>
  (function () {
    document.querySelectorAll('[data-salin]').forEach(function (tombol) {
      tombol.addEventListener('click', function () {
        var sumber = document.querySelector(tombol.getAttribute('data-salin'));
        if (!sumber) return;
        var teks = sumber.textContent.trim();
        var selesai = function () {
          var lama = tombol.textContent;
          tombol.textContent = 'Tersalin';
          setTimeout(function () { tombol.textContent = lama; }, 1600);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(teks).then(selesai).catch(function () {
            window.prompt('Salin nomor rekening ini:', teks);
          });
        } else {
          window.prompt('Salin nomor rekening ini:', teks);
        }
      });
    });
  })();
</script>
@endsection
