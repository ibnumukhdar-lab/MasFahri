@php $cerita = $cerita ?? null; $token = $token ?? null; @endphp
@extends('layouts.publik')

@section('judul', 'Isi Cerita Alumni')

@section('isi')
<div class="wrap" style="max-width:640px;padding:24px 0">
    <h1 class="text-navy" style="font-weight:800;font-size:1.6rem">Ceritamu untuk adik kelas</h1>
    <p class="muted kecil">Alumni SMA IT Arafah — isi di bawah, admin akan memeriksa lalu ceritamu tampil di situs.</p>

    @if (session('sukses'))
        <div class="pesan sukses">{{ session('sukses') }}</div>
    @endif
    @if ($errors->any())
        <div class="pesan galat">{{ $errors->first() }}</div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $cerita ? route('alumni.cerita.perbarui', $token) : route('alumni.cerita.simpan') }}"
          class="kartu" style="padding:16px;margin-top:14px">
        @csrf
        @if ($cerita)
            <input type="hidden" name="izin_tampil" value="1">
        @endif

        <div class="medan">
            <label for="nama">Nama lengkap *</label>
            <input id="nama" name="nama" value="{{ old('nama', $cerita->nama ?? '') }}" required>
        </div>
        <div class="dua">
            <div class="medan">
                <label for="wa">Nomor WhatsApp *</label>
                <input id="wa" name="wa" value="{{ old('wa', $cerita->wa ?? '') }}" required inputmode="tel">
            </div>
            <div class="medan">
                <label for="tahun_lulus">Tahun lulus</label>
                <input id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $cerita->tahun_lulus ?? '') }}" placeholder="2023">
            </div>
        </div>
        <div class="medan">
            <label for="kelas_akhir">Kelas akhir</label>
            <input id="kelas_akhir" name="kelas_akhir" value="{{ old('kelas_akhir', $cerita->kelas_akhir ?? '') }}" placeholder="XII MIPA 1">
        </div>

        <div class="dua">
            <div class="medan">
                <label for="status_saat_ini">Kamu sekarang</label>
                <select id="status_saat_ini" name="status_saat_ini">
                    <option value="">— pilih —</option>
                    @foreach (App\Models\AlumniCerita::STATUS as $kunci => $label)
                        <option value="{{ $kunci }}" @selected(old('status_saat_ini', $cerita->status_saat_ini ?? '') === $kunci)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="medan">
                <label for="status_keterangan">Di mana / di apa</label>
                <input id="status_keterangan" name="status_keterangan" value="{{ old('status_keterangan', $cerita->status_keterangan ?? '') }}" placeholder="Mis. UIN Antasari — Pendidikan Agama">
            </div>
        </div>

        <div class="medan">
            <label for="prestasi">Prestasi selama di Arafah (opsional)</label>
            <textarea id="prestasi" name="prestasi" rows="3" placeholder="Mis.&#10;Juara 2 MTQ kabupaten&#10;Hafalan 15 juz"></textarea>
            <small class="bantuan">Tulis satu prestasi per baris — nanti otomatis tampil sebagai daftar poin.</small>
        </div>

        <div class="medan">
            <label for="pesan">Pesan / nasihat untuk adik kelas *</label>
            <textarea id="pesan" name="pesan" rows="5" required
                placeholder="Ceritakan pengalamanmu di Arafah: yang paling berkesan, bagaimana menyeimbangkan sekolah dan tahfizh, dan nasihatmu untuk adik kelas.">{{ old('pesan', $cerita->pesan ?? '') }}</textarea>
        </div>

        <div class="medan">
            <label for="instagram">Instagram (opsional)</label>
            <input id="instagram" name="instagram" value="{{ old('instagram', $cerita->instagram ?? '') }}" placeholder="@nama">
        </div>

        <div class="medan">
            <label for="foto">Foto diri * {{ $cerita ? '(boleh diganti)' : '' }}</label>
            <input id="foto" name="foto" type="file" accept="image/*" {{ $cerita ? '' : 'required' }}>
            <span class="petunjuk">JPG/PNG maks 4 MB. Foto tampil di kartu ceritamu.</span>
        </div>

        @unless ($cerita)
            <label style="display:flex;gap:9px;align-items:flex-start;font-size:13px;margin-bottom:12px">
                <input type="checkbox" name="izin_tampil" value="1" style="width:auto;margin-top:2px">
                <span>Saya bersedia cerita dan foto saya ditampilkan di situs sekolah setelah diverifikasi admin.</span>
            </label>
        @endunless

        <button class="btn btn-utama btn-blok" type="submit">{{ $cerita ? 'Simpan perubahan' : 'Kirim cerita' }}</button>
    </form>
</div>

<style>
    .dua{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    @media(max-width:520px){.dua{grid-template-columns:minmax(0,1fr)}}
    input,select,textarea{max-width:100%}
    .bantuan{display:block;margin-top:5px;font-size:11.5px;color:#8a94a4}
</style>
@endsection
