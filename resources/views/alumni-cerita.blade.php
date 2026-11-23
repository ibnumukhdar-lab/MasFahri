@extends('layouts.publik')

@section('judul', 'Cerita Alumni')

@section('isi')
<style>
    .al-utama{max-width:1100px;margin:0 auto;padding:24px 16px 40px}
    .al-kepala{margin-bottom:18px}
    .al-judul{font-size:1.7rem;font-weight:800;color:#1f3a5f;line-height:1.2;margin-bottom:4px}
    .al-sub{font-size:13.5px;color:#5b6470;margin:0}
    .al-saring{display:flex;gap:8px;align-items:center;margin-top:12px;flex-wrap:wrap}
    .al-saring select{border:1.5px solid #dbe2ec;border-radius:10px;padding:9px 12px;font:inherit;font-size:13.5px;color:#1b2430;background:#fff;max-width:100%}
    .al-saring button{border:0;border-radius:10px;background:#1f3a5f;color:#fff;padding:9px 16px;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
    .al-tombol-baru{margin-left:auto;display:inline-flex;align-items:center;gap:6px;border-radius:10px;background:#2f7a5b;color:#fff;padding:9px 14px;font-size:13px;font-weight:700}
    .al-tombol-baru:hover{filter:brightness(1.08)}
    .al-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px}
    .al-kartu{background:#fff;border:1px solid #e5eaf1;border-radius:16px;overflow:hidden;
        box-shadow:0 8px 24px -18px rgba(16,24,40,.28);display:flex;flex-direction:column}
    .al-kartu img{width:100%;aspect-ratio:1/1;object-fit:cover;display:block;background:#eef2f8}
    .al-isi{padding:13px 14px 15px;display:flex;flex-direction:column;gap:6px;flex:1}
    .al-nama{font-size:14.5px;font-weight:800;color:#1f3a5f;line-height:1.3}
    .al-tag{display:flex;gap:5px;flex-wrap:wrap}
    .al-pil{font-size:10.5px;font-weight:700;color:#42506a;background:#eef2f8;border-radius:999px;padding:3px 9px}
    .al-status{font-size:12px;font-weight:700;color:#1f3a5f;margin-top:2px}
    .al-pesan{font-size:12.5px;color:#5b6470;line-height:1.55}
    .al-ig{font-size:12px;font-weight:700;color:#9c2f4f;text-decoration:none}
    .al-kosong{text-align:center;padding:40px 14px;color:#5b6470;font-size:14px}
    .al-kosong .ikon{width:52px;height:52px;border-radius:50%;background:#eef2f8;color:#1f3a5f;
        display:grid;place-items:center;margin:0 auto 10px;font-size:22px}
    .al-kosong a{display:inline-block;margin-top:12px;background:#2f7a5b;color:#fff;border-radius:10px;
        padding:10px 18px;font-weight:700;font-size:13.5px;text-decoration:none}
    .al-paginasi{margin-top:20px;display:flex;gap:6px;flex-wrap:wrap}
    .al-paginasi a,.al-paginasi span{min-width:34px;height:34px;padding:0 10px;border:1px solid #dbe2ec;
        border-radius:8px;background:#fff;display:grid;place-items:center;font-size:13px;text-decoration:none;color:#1b2430}
    .al-paginasi .aktif{background:#1f3a5f;color:#fff;border-color:#1f3a5f}
    @media(max-width:520px){
        .al-utama{padding:18px 12px 30px}
        .al-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:10px}
        .al-kartu img{border-radius:0}
        .al-tombol-baru{margin-left:0}
    }
</style>

<div class="al-utama">
    <div class="al-kepala">
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
                <article class="al-kartu">
                    <img src="{{ $a->foto_url }}" alt="{{ $a->nama }}" loading="lazy">
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
                            <p class="al-pesan">{{ \Illuminate\Support\Str::limit($a->pesan, 150) }}</p>
                        @endif
                        @if ($a->instagram)
                            <a class="al-ig" href="https://instagram.com/{{ trim($a->instagram, '@') }}" target="_blank" rel="noopener">Instagram ↗</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="al-kosong">
            <div class="ikon" style="width:52px;height:52px;border-radius:50%;background:#eef2f8;color:#1f3a5f;display:grid;place-items:center;margin:0 auto 10px">
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
