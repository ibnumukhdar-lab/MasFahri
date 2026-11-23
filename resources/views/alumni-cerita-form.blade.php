@php $cerita = $cerita ?? null; $token = $token ?? null; @endphp
@extends('layouts.publik')

@section('judul', 'Isi Cerita Alumni')

<style>
    .ac-utama{max-width:640px;margin:0 auto;padding:26px 18px 40px}
    .ac-judul{font-size:1.55rem;font-weight:800;color:var(--navy,#1f3a5f);line-height:1.2;margin-bottom:6px}
    .ac-sub{font-size:13px;color:#5b6470;margin:0 0 18px}
    .ac-kartu{background:#fff;border:1px solid #e5eaf1;border-radius:18px;padding:20px 18px;box-shadow:0 10px 30px -20px rgba(16,24,40,.25)}
    .ac-medan{margin-bottom:16px}
    .ac-medan label{display:block;font-size:12px;font-weight:700;color:#3b4453;margin-bottom:6px;letter-spacing:.01em}
    .ac-medan input[type=text],
    .ac-medan input[type=tel],
    .ac-medan input[type=email],
    .ac-medan select,
    .ac-medan textarea{
        width:100%;max-width:100%;border:1.5px solid #dbe2ec;border-radius:12px;
        padding:12px 14px;font:inherit;font-size:14.5px;color:#1b2430;background:#fff;
        transition:border-color .15s, box-shadow .15s}
    .ac-medan input:focus, .ac-medan select:focus, .ac-medan textarea:focus{
        outline:none;border-color:#1f3a5f;box-shadow:0 0 0 3px rgba(31,58,95,.10)}
    .ac-medan textarea{resize:vertical;line-height:1.6}
    .ac-dua{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .ac-bantuan{display:block;font-size:11.5px;color:#7a8494;margin-top:5px}
    .ac-salah{display:block;font-size:12px;color:#b4304a;font-weight:600;margin-top:4px}
    .ac-setuju{display:flex;gap:10px;align-items:flex-start;background:#f4f7fb;border:1px solid #e5eaf1;
        border-radius:12px;padding:12px 14px;margin:18px 0 16px}
    .ac-setuju input{width:18px;height:18px;flex:none;margin-top:2px;accent-color:#1f3a5f}
    .ac-setuju span{font-size:12.5px;color:#42506a;line-height:1.55}
    .ac-tombol{width:100%;border:0;border-radius:12px;background:#1f3a5f;color:#fff;
        padding:14px;font:inherit;font-weight:800;font-size:15px;cursor:pointer;transition:.15s}
    .ac-tombol:hover{background:#162c47}
    .ac-tombol:active{transform:translateY(1px)}
    .ac-garis-potong{border:1.5px dashed #c9d4e3;border-radius:14px;padding:14px;text-align:center;
        color:#5b6470;font-size:13px;cursor:pointer;transition:.15s}
    .ac-garis-potong:hover{border-color:#1f3a5f;background:#f8fafc}
    .ac-garis-potong input{display:none}
    .ac-foto-terpilih{font-size:12px;color:#256248;font-weight:700;margin-top:8px}
    .ac-bagian{font-size:10.5px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;
        color:#8a94a4;margin:20px 0 10px}
    .ac-bagian:first-of-type{margin-top:0}
    @media(max-width:520px){.ac-dua{grid-template-columns:minmax(0,1fr)}}
</style>

@section('isi')
<div class="ac-utama">
    <h1 class="ac-judul">Ceritamu untuk adik kelas</h1>
    <p class="ac-sub">Alumni SMA IT Arafah — isi di bawah, admin akan memeriksa lalu ceritamu tampil di situs.</p>

    @if (session('sukses'))
        <div class="pesan sukses" style="margin-bottom:14px">{{ session('sukses') }}</div>
    @endif
    @if ($errors->any())
        <div class="pesan galat" style="margin-bottom:14px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $cerita ? route('alumni.cerita.perbarui', $token) : route('alumni.cerita.simpan') }}"
          class="ac-kartu">
        @csrf
        @if ($cerita)
            <input type="hidden" name="izin_tampil" value="1">
        @endif

        <p class="ac-bagian">Data diri</p>

        <div class="ac-medan">
            <label for="nama">Nama lengkap *</label>
            <input id="nama" type="text" name="nama" value="{{ old('nama', $cerita->nama ?? '') }}" required autocomplete="name" placeholder="Nama lengkap">
            @error('nama') <span class="ac-salah">{{ $message }}</span> @enderror
        </div>

        <div class="ac-dua">
            <div class="ac-medan">
                <label for="wa">Nomor WhatsApp *</label>
                <input id="wa" type="tel" name="wa" value="{{ old('wa', $cerita->wa ?? '') }}" required inputmode="tel" autocomplete="tel" placeholder="08xx xxxx xxxx">
                @error('wa') <span class="ac-salah">{{ $message }}</span> @enderror
            </div>
            <div class="ac-medan">
                <label for="tahun_lulus">Tahun lulus</label>
                <input id="tahun_lulus" type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $cerita->tahun_lulus ?? '') }}" placeholder="2023" inputmode="numeric" maxlength="8">
            </div>
        </div>

        <div class="ac-dua">
            <div class="ac-medan">
                <label for="kelas_akhir">Kelas akhir</label>
                <input id="kelas_akhir" type="text" name="kelas_akhir" value="{{ old('kelas_akhir', $cerita->kelas_akhir ?? '') }}" placeholder="XII MIPA 1">
            </div>
            <div class="ac-medan">
                <label for="instagram">Instagram</label>
                <input id="instagram" type="text" name="instagram" value="{{ old('instagram', $cerita->instagram ?? '') }}" placeholder="@nama">
            </div>
        </div>

        <p class="ac-bagian">Sekarang kamu sedang apa?</p>

        <div class="ac-dua">
            <div class="ac-medan">
                <label for="status_saat_ini">Status</label>
                <select id="status_saat_ini" name="status_saat_ini">
                    <option value="">— pilih —</option>
                    @foreach (App\Models\AlumniCerita::STATUS as $kunci => $label)
                        <option value="{{ $kunci }}" @selected(old('status_saat_ini', $cerita->status_saat_ini ?? '') === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ac-medan">
                <label for="status_keterangan">Di mana / di apa</label>
                <input id="status_keterangan" type="text" name="status_keterangan" value="{{ old('status_keterangan', $cerita->status_keterangan ?? '') }}" placeholder="UIN Antasari — PAI">
            </div>
        </div>
        <span class="ac-bantuan">Kosongkan bila tidak ingin menyebutkannya.</span>

        <p class="ac-bagian">Cerita &amp; prestasi</p>

        <div class="ac-medan">
            <label for="prestasi">Prestasi selama di Arafah</label>
            <textarea id="prestasi" name="prestasi" rows="3" placeholder="Mis. Juara 2 MTQ kabupaten, hafalan 15 juz">{{ old('prestasi', $cerita->prestasi ?? '') }}</textarea>
            <span class="ac-bantuan">Opsional — boleh dikosongkan.</span>
            @error('prestasi') <span class="ac-salah">{{ $message }}</span> @enderror
        </div>

        <div class="ac-medan">
            <label for="pesan">Pesan / nasihat untuk adik kelas *</label>
            <textarea id="pesan" name="pesan" rows="6" required
                placeholder="Ceritakan pengalamanmu di Arafah: yang paling berkesan, bagaimana menyeimbangkan sekolah dan tahfizh, dan nasihatmu untuk adik kelas.">{{ old('pesan', $cerita->pesan ?? '') }}</textarea>
            @error('pesan') <span class="ac-salah">{{ $message }}</span> @enderror
        </div>

        <p class="ac-bagian">Foto &amp; persetujuan</p>

        <div class="ac-medan">
            <label for="foto">Foto diri *</label>
            <label class="ac-garis-potong" for="foto">
                <div id="ac-foto-teks">{{ $cerita ? 'Tekan untuk mengganti foto' : 'Tekan untuk memilih foto' }}</div>
                <input id="foto" name="foto" type="file" accept="image/*" {{ $cerita ? '' : 'required' }}
                       onchange="document.getElementById('ac-foto-teks').textContent = this.files[0] ? this.files[0].name : 'Tekan untuk memilih foto'; document.getElementById('ac-foto-terpilih').textContent = 'Foto siap dikirim ✓';">
            </label>
            <span class="ac-bantuan">JPG/PNG maks 4 MB. Foto tampil di kartu ceritamu.</span>
            <span class="ac-foto-terpilih" id="ac-foto-terpilih">{{ $cerita ? 'Foto lama tetap terpakai bila tidak diganti' : '' }}</span>
            @error('foto') <span class="ac-salah">{{ $message }}</span> @enderror
        </div>

        @unless ($cerita)
            <label class="ac-setuju">
                <input type="checkbox" name="izin_tampil" value="1">
                <span>Saya bersedia cerita dan foto saya <b>ditampilkan di situs sekolah</b> setelah diverifikasi admin.</span>
            </label>
        @endunless

        <button class="ac-tombol" type="submit">{{ $cerita ? 'Simpan perubahan' : 'Kirim cerita' }}</button>
    </form>
</div>
@endsection
