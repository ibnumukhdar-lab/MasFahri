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
    document.addEventListener('DOMContentLoaded', () => {
        const f = document.getElementById('foto');
        const l = document.getElementById('af-nama-berkas');
        if (f) f.addEventListener('change', () => {
            l.textContent = f.files.length ? f.files[0].name : 'Tekan untuk memilih foto';
        });
    });
</script>
@endsection
