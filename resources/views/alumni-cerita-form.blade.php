@extends('layouts.publik')

@section('judul', 'Isi Cerita Alumni')

@section('isi')
@php $cerita = $cerita ?? null; $token = $token ?? null; @endphp
<style>
    .af-utama{max-width:640px;margin:0 auto;padding:32px 18px 48px}
    @media(min-width:768px){.af-utama{max-width:720px;padding:48px 24px 60px}}

    .af-kepala{text-align:center;margin-bottom:24px}
    .af-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#9c2f4f;margin-bottom:6px}
    .af-judul{font-size:1.7rem;font-weight:800;color:#1f3a5f;line-height:1.15;margin-bottom:8px}
    .af-sub{font-size:14px;color:#5b6470;line-height:1.6;margin:0}

    .af-kartu{background:#fff;border:1px solid #e7ecf3;border-radius:20px;padding:22px 18px;
        box-shadow:0 14px 40px -24px rgba(16,24,40,.35)}
    @media(min-width:768px){.af-kartu{padding:30px 32px 28px}}

    .af-medan{margin-bottom:16px}
    .af-medan:last-of-type{margin-bottom:0}
    .af-medan label{display:block;font-size:12.5px;font-weight:700;color:#3d4a5c;margin-bottom:6px}
    .af-medan label em{font-style:normal;color:#9c2f4f}
    .af-medan input[type=text],.af-medan input[type=tel],.af-medan input:not([type]),
    .af-medan input[type=file],.af-medan select,.af-medan textarea{
        display:block;width:100%;box-sizing:border-box;border:1.5px solid #dbe2ec;border-radius:12px;
        padding:11px 14px;font:inherit;font-size:14px;color:#1b2430;background:#fff;transition:border-color .15s, box-shadow .15s}
    .af-medan textarea{resize:vertical;min-height:96px;line-height:1.6}
    .af-medan input:focus,.af-medan select:focus,.af-medan textarea:focus{
        outline:none;border-color:#1f3a5f;box-shadow:0 0 0 3px rgba(31,58,95,.1)}
    .af-medan input::placeholder,.af-medan textarea::placeholder{color:#a6afbd}
    .af-bantuan{display:block;margin-top:5px;font-size:11.5px;color:#8a94a4;line-height:1.5}
    .af-petunjuk{display:block;margin-top:5px;font-size:11.5px;color:#8a94a4}

    .af-dua{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    @media(max-width:560px){.af-dua{grid-template-columns:minmax(0,1fr)}}

    /* unggah foto jadi kotak dropzone */
    .af-unggah{position:relative;display:flex;align-items:center;gap:12px;border:1.5px dashed #c9d4e3;
        border-radius:14px;padding:14px 16px;background:#f8fafd;cursor:pointer;transition:border-color .15s, background .15s}
    .af-unggah:hover{border-color:#1f3a5f;background:#f2f6fb}
    .af-unggah input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer}
    .af-unggah .kotak-ikon{width:38px;height:38px;border-radius:10px;background:#eef2f8;color:#1f3a5f;display:grid;place-items:center;flex:none}
    .af-unggah b{display:block;font-size:13px;color:#1f3a5f}
    .af-unggah small{font-size:11.5px;color:#8a94a4}

    /* ---------- PANGKAS FOTO (crop) ---------- */
    .af-crop{display:none;margin-top:12px;border:1px solid #e7ecf3;border-radius:14px;
        background:#f8fafd;padding:14px}
    .af-crop.buka{display:block}
    .af-crop-papan{position:relative;width:100%;aspect-ratio:1/1;border-radius:12px;overflow:hidden;
        background:#dde5ef;touch-action:none;cursor:grab}
    .af-crop-papan.geser{cursor:grabbing}
    .af-crop-papan img{position:absolute;top:0;left:0;transform-origin:0 0;user-select:none;
        -webkit-user-drag:none;max-width:none;pointer-events:none}
    .af-crop-bantuan{display:flex;align-items:center;gap:10px;margin-top:12px}
    .af-crop-bantuan label{margin:0;font-weight:600;flex:none}
    .af-crop-bantuan input[type=range]{flex:1;accent-color:#1f3a5f}
    .af-crop-aksi{display:flex;gap:10px;margin-top:12px}
    .af-crop-aksi button{flex:1;border:0;border-radius:10px;padding:10px 12px;font:inherit;
        font-size:13px;font-weight:700;cursor:pointer}
    .af-crop-aksi .pas{background:#eef2f8;color:#1f3a5f}
    .af-crop-aksi .pas:hover{background:#e2e9f3}
    .af-crop-aksi .ulang{background:#fdecee;color:#8c2430}
    .af-crop-hint{margin:8px 0 0;font-size:11.5px;color:#8a94a4}

    /* persetujuan */
    .af-setuju{display:flex;gap:10px;align-items:flex-start;background:#f4f7fb;border:1px solid #e5eaf1;
        border-radius:14px;padding:13px 14px;margin:18px 0 16px}
    .af-setuju input[type=checkbox]{width:17px;height:17px;margin-top:2px;accent-color:#2f7a5b;flex:none}
    .af-setuju span{font-size:12.5px;color:#3d4a5c;line-height:1.55}

    .af-kirim{display:block;width:100%;border:0;border-radius:14px;background:#1f3a5f;color:#fff;
        padding:13px 18px;font:inherit;font-size:14.5px;font-weight:800;cursor:pointer;transition:background .15s}
    .af-kirim:hover{background:#182c47}

    .af-pesan{border-radius:14px;padding:12px 15px;font-size:13px;margin-bottom:16px}
    .af-pesan.sukses{background:#e9f6ef;color:#22633f;border:1px solid #bfe3cd}
    .af-pesan.galat{background:#fdecee;color:#8c2430;border:1px solid #f5c2ca}
</style>

<div class="af-utama">
    <div class="af-kepala">
        <div class="af-eyebrow">Cerita alumni</div>
        <h1 class="af-judul">Ceritamu untuk adik kelas</h1>
        <p class="af-sub">Alumni SMA IT Arafah — isi di bawah, admin akan memeriksa lalu ceritamu tampil di situs.</p>
    </div>

    @if (session('sukses'))
        <div class="af-pesan sukses">{{ session('sukses') }}</div>
    @endif
    @if ($errors->any())
        <div class="af-pesan galat">{{ $errors->first() }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $cerita ? route('alumni.cerita.perbarui', $token) : route('alumni.cerita.simpan') }}"
          class="af-kartu">
        @csrf
        @if ($cerita)
            <input type="hidden" name="izin_tampil" value="1">
        @endif

        <div class="af-medan">
            <label for="nama">Nama lengkap <em>*</em></label>
            <input id="nama" name="nama" value="{{ old('nama', $cerita->nama ?? '') }}" required>
        </div>
        <div class="af-dua">
            <div class="af-medan">
                <label for="wa">Nomor WhatsApp <em>*</em></label>
                <input id="wa" name="wa" value="{{ old('wa', $cerita->wa ?? '') }}" required inputmode="tel" placeholder="0812 3456 7890">
            </div>
            <div class="af-medan">
                <label for="tahun_lulus">Tahun lulus</label>
                <input id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $cerita->tahun_lulus ?? '') }}" placeholder="2023">
            </div>
        </div>
        <div class="af-dua">
            <div class="af-medan">
                <label for="kelas_akhir">Kelas akhir</label>
                <input id="kelas_akhir" name="kelas_akhir" value="{{ old('kelas_akhir', $cerita->kelas_akhir ?? '') }}" placeholder="XII MIPA 1">
            </div>
            <div class="af-medan">
                <label for="status_saat_ini">Kamu sekarang</label>
                <select id="status_saat_ini" name="status_saat_ini">
                    <option value="">— pilih —</option>
                    @foreach (App\Models\AlumniCerita::STATUS as $kunci => $label)
                        <option value="{{ $kunci }}" @selected(old('status_saat_ini', $cerita->status_saat_ini ?? '') === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="af-medan">
            <label for="status_keterangan">Di mana / di apa</label>
            <input id="status_keterangan" name="status_keterangan" value="{{ old('status_keterangan', $cerita->status_keterangan ?? '') }}" placeholder="Mis. UIN Antasari — Pendidikan Agama">
        </div>

        <div class="af-medan">
            <label for="prestasi">Prestasi selama di Arafah (opsional)</label>
            <textarea id="prestasi" name="prestasi" rows="3" placeholder="Mis.&#10;Juara 2 MTQ kabupaten&#10;Hafalan 15 juz"></textarea>
            <small class="af-bantuan">Tulis satu prestasi per baris — nanti otomatis tampil sebagai daftar poin.</small>
        </div>

        <div class="af-medan">
            <label for="pesan">Pesan / nasihat untuk adik kelas <em>*</em></label>
            <textarea id="pesan" name="pesan" rows="5" required
                placeholder="Ceritakan pengalamanmu di Arafah: yang paling berkesan, bagaimana menyeimbangkan sekolah dan tahfizh, dan nasihatmu untuk adik kelas.">{{ old('pesan', $cerita->pesan ?? '') }}</textarea>
        </div>

        <div class="af-medan">
            <label for="instagram">Instagram (opsional)</label>
            <input id="instagram" name="instagram" value="{{ old('instagram', $cerita->instagram ?? '') }}" placeholder="@nama">
        </div>

        <div class="af-medan">
            <label for="foto">Foto diri <em>*</em> {{ $cerita ? '(boleh diganti)' : '' }}</label>
            <div class="af-unggah">
                <input id="foto" name="foto" type="file" accept="image/*" {{ $cerita ? '' : 'required' }}>
                <div class="kotak-ikon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2z"/><circle cx="9" cy="9" r="1.6"/><path d="M21 15l-5-5L5 21"/></svg>
                </div>
                <div>
                    <b id="af-nama-berkas">Tekan untuk memilih foto</b>
                    <small>JPG/PNG maksimal 4 MB — tampil di kartu ceritamu</small>
                </div>
            </div>
            <div class="af-crop" id="af-crop">
                <div class="af-crop-papan" id="af-crop-papan">
                    <img id="af-crop-img" alt="Pratinjau foto yang akan dipangkas">
                </div>
                <div class="af-crop-bantuan">
                    <label for="af-crop-zoom">Zoom</label>
                    <input type="range" id="af-crop-zoom" min="1" max="3" step="0.01" value="1">
                </div>
                <div class="af-crop-aksi">
                    <button type="button" class="pas" id="af-crop-pas">Pas di kotak</button>
                    <button type="button" class="ulang" id="af-crop-ulang">Ganti foto</button>
                </div>
                <p class="af-crop-hint">Geser foto di kotak sampai wajahmu pas di tengah, lalu kirim. Kotak ini bentuknya sama dengan foto di halaman alumni (persegi).</p>
            </div>
        </div>

        @unless ($cerita)
            <label class="af-setuju">
                <input type="checkbox" name="izin_tampil" value="1" required>
                <span>Saya bersedia cerita dan foto saya ditampilkan di situs sekolah setelah diverifikasi admin.</span>
            </label>
        @endunless

        <button class="af-kirim" type="submit">{{ $cerita ? 'Simpan perubahan' : 'Kirim cerita' }}</button>
    </form>
</div>

<script>
    // ---------- Pangkas foto: geser + zoom, hasil ditempel ke input file saat submit ----------
    document.addEventListener('DOMContentLoaded', () => {
        const f = document.getElementById('foto');
        const l = document.getElementById('af-nama-berkas');
        const kotak = document.getElementById('af-crop');
        const papan = document.getElementById('af-crop-papan');
        const img = document.getElementById('af-crop-img');
        const zoom = document.getElementById('af-crop-zoom');
        const tombolPas = document.getElementById('af-crop-pas');
        const tombolUlang = document.getElementById('af-crop-ulang');
        const formulir = document.querySelector('form.af-kartu');

        if (!f || !kotak) return;

        let berkasAsli = null;      // File yang dipilih pengguna
        let gambar = null;          // HTMLImageElement hasil decode
        let skalaDasar = 1;         // skala agar gambar memenuhi sisi terpanjang papan
        let geserX = 0, geserY = 0; // posisi geser dalam piksel papan
        let sedangGeser = false, sebelumX = 0, sebelumY = 0;

        const bacaPapan = () => {
            const r = papan.getBoundingClientRect();
            return { w: r.width, h: r.height };
        };

        const gambarUlang = () => {
            if (!gambar) return;
            const { w: pw, h: ph } = bacaPapan();
            const s = skalaDasar * parseFloat(zoom.value);
            img.style.width = (gambar.naturalWidth * s) + 'px';
            img.style.height = (gambar.naturalHeight * s) + 'px';
            img.src = gambar.src;

            // kunci geser agar gambar selalu menutupi papan
            const gw = gambar.naturalWidth * s, gh = gambar.naturalHeight * s;
            geserX = Math.min(0, Math.max(pw - gw, geserX));
            geserY = Math.min(0, Math.max(ph - gh, geserY));
            img.style.left = geserX + 'px';
            img.style.top = geserY + 'px';
        };

        const pasKotak = () => {
            if (!gambar) return;
            const { w: pw, h: ph } = bacaPapan();
            skalaDasar = Math.max(pw / gambar.naturalWidth, ph / gambar.naturalHeight);
            zoom.value = '1';
            const s = skalaDasar;
            geserX = (pw - gambar.naturalWidth * s) / 2;
            geserY = (ph - gambar.naturalHeight * s) / 2;
            gambarUlang();
        };

        const mulai = (berkas) => {
            berkasAsli = berkas;
            l.textContent = berkas.name;
            const url = URL.createObjectURL(berkas);
            gambar = new Image();
            gambar.onload = () => {
                kotak.classList.add('buka');
                requestAnimationFrame(pasKotak);
                gambar.src = url;
            };
            gambar.src = url;
        };

        f.addEventListener('change', () => {
            if (f.files && f.files[0]) mulai(f.files[0]);
        });

        // geser dengan mouse + sentuhan
        const turun = (x, y) => {
            if (!gambar) return;
            sedangGeser = true; sebelumX = x; sebelumY = y;
            papan.classList.add('geser');
        };
        const gerak = (x, y) => {
            if (!sedangGeser || !gambar) return;
            const { w: pw, h: ph } = bacaPapan();
            const s = skalaDasar * parseFloat(zoom.value);
            const gw = gambar.naturalWidth * s, gh = gambar.naturalHeight * s;
            geserX = Math.min(0, Math.max(pw - gw, geserX + (x - sebelumX)));
            geserY = Math.min(0, Math.max(ph - gh, geserY + (y - sebelumY)));
            sebelumX = x; sebelumY = y;
            img.style.left = geserX + 'px';
            img.style.top = geserY + 'px';
        };
        papan.addEventListener('mousedown', e => { turun(e.clientX, e.clientY); e.preventDefault(); });
        window.addEventListener('mousemove', e => gerak(e.clientX, e.clientY));
        window.addEventListener('mouseup', () => { sedangGeser = false; papan.classList.remove('geser'); });
        papan.addEventListener('touchstart', e => { turun(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
        papan.addEventListener('touchmove', e => { e.preventDefault(); gerak(e.touches[0].clientX, e.touches[0].clientY); }, { passive: false });
        papan.addEventListener('touchend', () => { sedangGeser = false; papan.classList.remove('geser'); });

        zoom.addEventListener('input', gambarUlang);
        window.addEventListener('resize', () => { if (gambar) pasKotak(); });

        tombolPas.addEventListener('click', pasKotak);
        tombolUlang.addEventListener('click', () => {
            berkasAsli = null; gambar = null;
            kotak.classList.remove('buka');
            f.value = '';
            l.textContent = 'Tekan untuk memilih foto';
        });

        // pangkas + tempel hasil ke input file tepat sebelum submit
        if (formulir) formulir.addEventListener('submit', (e) => {
            if (!gambar || !berkasAsli) return; // tanpa foto (mis. edit tanpa ganti)
            e.preventDefault();
            const { w: pw, h: ph } = bacaPapan();
            const s = skalaDasar * parseFloat(zoom.value);
            const kanvas = document.createElement('canvas');
            kanvas.width = Math.round(pw); kanvas.height = Math.round(ph);
            const g = kanvas.getContext('2d');
            g.imageSmoothingQuality = 'high';
            g.drawImage(gambar, -geserX / s, -geserY / s, gambar.naturalWidth, gambar.naturalHeight);
            kanvas.toBlob((blob) => {
                const baru = new File([blob], berkasAsli.name.replace(/\.[^.]+$/, '') + '-pangkas.jpg',
                    { type: 'image/jpeg', lastModified: Date.now() });
                const dt = new DataTransfer();
                dt.items.add(baru);
                f.files = dt.files;
                formulir.submit();
            }, 'image/jpeg', 0.9);
        }, true);
    });
</script>
@endsection
