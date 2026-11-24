@extends('layouts.publik')

@section('judul', 'Cerita Alumni')

@section('isi')
<style>
    .al-utama{max-width:1100px;margin:0 auto;padding:28px 16px 44px}
    .al-kepala{text-align:center;max-width:560px;margin:0 auto 22px}
    .al-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#9c2f4f;margin-bottom:6px}
    .al-judul{font-size:1.9rem;font-weight:800;color:#1f3a5f;line-height:1.15;margin-bottom:6px}
    .al-sub{font-size:14px;color:#5b6470;line-height:1.6;margin:0}
    .al-saring{display:flex;gap:8px;align-items:center;justify-content:center;margin-top:16px;flex-wrap:wrap}
    .al-saring select{border:1.5px solid #dbe2ec;border-radius:999px;padding:9px 14px;font:inherit;font-size:13.5px;color:#1b2430;background:#fff;max-width:100%}
    .al-saring button{border:0;border-radius:999px;background:#1f3a5f;color:#fff;padding:9px 18px;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
    .al-tombol-baru{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#2f7a5b;color:#fff;padding:9px 16px;font-size:13px;font-weight:700}
    .al-tombol-baru:hover{filter:brightness(1.08)}

    /* --- grid kartu --- */
    .al-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px}
    .al-kartu{background:#fff;border:1px solid #e7ecf3;border-radius:18px;overflow:hidden;text-decoration:none;color:inherit;
        box-shadow:0 10px 28px -20px rgba(16,24,40,.32);display:flex;flex-direction:column;min-width:0;
        transition:transform .15s ease, box-shadow .15s ease}
    .al-kartu:hover{transform:translateY(-3px);box-shadow:0 16px 36px -20px rgba(16,24,40,.4)}
    .al-foto{width:100%;aspect-ratio:1/1;object-fit:cover;display:block;background:#eef2f8}
    .al-isi{padding:14px 16px 16px;display:flex;flex-direction:column;gap:7px;flex:1;min-width:0}
    .al-nama{font-size:15px;font-weight:800;color:#1f3a5f;line-height:1.3;overflow-wrap:anywhere}
    .al-tag{display:flex;gap:5px;flex-wrap:wrap}
    .al-pil{font-size:10.5px;font-weight:700;color:#42506a;background:#eef2f8;border-radius:999px;padding:3px 10px}
    .al-status{font-size:12px;font-weight:700;color:#9c2f4f;margin:0;overflow-wrap:anywhere}
    .al-pesan{font-size:12.5px;color:#5b6470;line-height:1.6;margin:0;flex:1;overflow-wrap:anywhere}
    .al-ig{align-self:flex-start;font-size:12px;font-weight:700;color:#9c2f4f;background:#f8ecf0;border-radius:999px;padding:4px 12px;text-decoration:none;margin-top:2px}

    /* --- lainnya --- */
    .al-kosong{text-align:center;padding:44px 14px;color:#5b6470;font-size:14px;background:#fff;border:1px dashed #dbe2ec;border-radius:18px}
    .al-kosong .ikon{width:54px;height:54px;border-radius:50%;background:#eef2f8;color:#1f3a5f;display:grid;place-items:center;margin:0 auto 12px}
    .al-kosong b{color:#1f3a5f}
    .al-kosong a{display:inline-block;margin-top:14px;background:#2f7a5b;color:#fff;border-radius:999px;padding:10px 20px;font-weight:700;font-size:13.5px;text-decoration:none}
    .al-paginasi{margin-top:24px;display:flex;gap:6px;flex-wrap:wrap;justify-content:center}
    .al-paginasi a,.al-paginasi span{min-width:36px;height:36px;padding:0 12px;border:1px solid #dbe2ec;border-radius:999px;background:#fff;display:grid;place-items:center;font-size:13px;text-decoration:none;color:#1b2430}
    .al-paginasi .aktif{background:#1f3a5f;color:#fff;border-color:#1f3a5f}

    /* --- HP: SATU KOLOM — foto penuh, teks di bawah, tidak dipaksa 2 kolom sempit --- */
    @media(max-width:640px){
        .al-utama{padding:20px 14px 34px}
        .al-judul{font-size:1.55rem}
        .al-grid{grid-template-columns:minmax(0,1fr);gap:14px;max-width:420px;margin:0 auto}
        .al-saring{gap:6px}
        .al-tombol-baru{width:100%;justify-content:center}
    }
</style>

<div class="al-utama">
    <div class="al-kepala">
        <div class="al-eyebrow">Jejak setelah lulus</div>
        <h1 class="al-judul">Cerita Alumni</h1>
        <p class="al-sub">Pengalaman dan pesan para alumni SMA IT Arafah untuk adik-adik kelas.</p>
        <form method="GET" class="al-saring">
            <select name="angkatan">
                <option value="">Semua angkatan</option>
                @foreach ($angkatan as $a)
                    <option value="{{ $a }}" @selected(request('angkatan') === $a)>{{ $a }}</option>
                @endforeach
            </select>
            <button type="submit">Saring</button>
            <a class="al-tombol-baru" href="{{ route('alumni.cerita.form') }}">+ Tulis ceritamu</a>
        </form>
    </div>

    @if (session('sukses'))
        <div class="pesan sukses" style="margin-bottom:14px">{{ session('sukses') }}</div>
    @endif

    @if ($alumni->count())
        <div class="al-grid">
            @foreach ($alumni as $a)
                <a class="al-kartu" href="{{ url($a->url_halaman) }}">
                    <img class="al-foto" src="{{ $a->foto_url }}" alt="{{ $a->nama }}" loading="lazy">
                    <div class="al-isi">
                        <h3 class="al-nama">{{ $a->nama }}</h3>
                        <div class="al-tag">
                            @if ($a->tahun_lulus)<span class="al-pil">Lulus {{ $a->tahun_lulus }}</span>@endif
                            @if ($a->kelas_akhir)<span class="al-pil">{{ $a->kelas_akhir }}</span>@endif
                        </div>
                        @if ($a->status_rangkum)
                            <p class="al-status">{{ $a->status_rangkum }}</p>
                        @endif
                        @if ($a->pesan)
                            <p class="al-pesan">{{ \Illuminate\Support\Str::words($a->pesan, 22, '…') }}</p>
                        @endif
                        @if ($a->instagram)
                            <span class="al-ig">Instagram&nbsp;↗</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="al-kosong">
            <div class="ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" style="width:24px;height:24px"><path d="M4 19V5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2zm2 0h13v-2H6"/><path d="M8 7h8M8 11h6"/></svg>
            </div>
            <b>Belum ada cerita alumni yang tampil</b>
            <p class="kecil">Isian baru menunggu verifikasi admin.</p>
            <a href="{{ route('alumni.cerita.form') }}">Tulis ceritamu duluan</a>
        </div>
    @endif

    @if ($alumni->hasPages())
        <div class="al-paginasi">{{ $alumni->onEachSide(1)->links() }}</div>
    @endif
</div>
@endsection
