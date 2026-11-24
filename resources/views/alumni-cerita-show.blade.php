@extends('layouts.publik')

@section('judul', 'Cerita ' . $cerita->nama)

@section('isi')
<style>
    .ak-utama{max-width:680px;margin:0 auto;padding:24px 16px 40px}
    .ak-kembali{display:inline-flex;align-items:center;gap:6px;color:#1f3a5f;font-size:13px;font-weight:700;text-decoration:none;margin-bottom:14px}
    .ak-kembali:hover{color:#9c2f4f}
    .ak-kepala{text-align:center;margin-bottom:20px}
    .ak-foto{width:180px;height:180px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 14px;
        border:4px solid #fff;box-shadow:0 12px 32px -16px rgba(16,24,40,.4)}
    .ak-nama{font-size:1.9rem;font-weight:800;color:#1f3a5f;line-height:1.15;margin-bottom:6px}
    .ak-tag{display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:10px}
    .ak-pil{font-size:11px;font-weight:700;color:#42506a;background:#eef2f8;border-radius:999px;padding:4px 11px}
    .ak-status{font-size:14px;font-weight:700;color:#1f3a5f;margin:0}
    .ak-status small{display:block;font-size:12px;font-weight:600;color:#5b6470}
    .ak-kotak{background:#fff;border:1px solid #e5eaf1;border-radius:16px;padding:22px 20px;
        box-shadow:0 8px 24px -18px rgba(16,24,40,.3);margin-bottom:14px}
    .ak-kotak h2{font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;
        color:#8a94a4;margin:0 0 10px}
    .ak-prestasi ul{list-style:none;margin:0;padding:0}
    .ak-prestasi li{font-size:13.5px;color:#42506a;line-height:1.7;padding-left:18px;position:relative;overflow-wrap:anywhere}
    .ak-prestasi li::before{content:"";position:absolute;left:2px;top:.62em;width:7px;height:7px;border-radius:50%;background:#9c2f4f}
    .ak-cerita p{font-size:15px;line-height:1.85;color:#2b3444;margin:0 0 1em}
    .ak-cerita p:last-child{margin-bottom:0}
    .ak-sisi{background:#f4f7fb;border:1px solid #e5eaf1;border-radius:16px;padding:16px 18px;margin-bottom:14px}
    .ak-sisi h2{font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#8a94a4;margin:0 0 8px}
    .ak-sisi p{font-size:13.5px;color:#2b3444;line-height:1.7;margin:0}
    .ak-sisikan{display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:700;color:#1f3a5f}
    .ak-ig{display:inline-flex;align-items:center;gap:6px;background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);
        color:#fff;border-radius:10px;padding:9px 15px;font-size:13px;font-weight:700;text-decoration:none;margin-top:10px}
    .ak-lain{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-top:16px}
    .al-lain-kartu{display:block;background:#fff;border:1px solid #e5eaf1;border-radius:14px;padding:10px;
        text-decoration:none;color:inherit;text-align:center}
    .al-lain-kartu img{width:64px;height:64px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 6px}
    .al-lain-kartu b{font-size:12.5px;color:#1f3a5f;display:block;line-height:1.3}
    .al-lain-kartu small{font-size:10.5px;color:#7a8494}
    @media(max-width:520px){
        .ak-utama{padding:16px 12px 30px}
        .ak-nama{font-size:1.5rem}
        .ak-foto{width:150px;height:150px}
    }
</style>

<div class="ak-utama">
    <a class="ak-kembali" href="{{ route('alumni.cerita') }}">← Semua cerita alumni</a>

    <div class="ak-kepala">
        <img class="ak-foto" src="{{ $cerita->foto_url }}" alt="{{ $cerita->nama }}">
        <h1 class="ak-nama">{{ $cerita->nama }}</h1>
        <div class="ak-tag">
            @if ($cerita->tahun_lulus)<span class="ak-pil">Lulus {{ $cerita->tahun_lulus }}</span>@endif
            @if ($cerita->kelas_akhir)<span class="ak-pil">{{ $cerita->kelas_akhir }}</span>@endif
        </div>
        @if ($cerita->status_rangkum)
            <p class="ak-status">
                {{ $cerita->status_label }}
                @if ($cerita->status_keterangan)<small>{{ $cerita->status_keterangan }}</small>@endif
            </p>
        @endif
    </div>

    @if ($cerita->prestasi)
        <div class="ak-sisi ak-prestasi">
            <h2>Prestasi selama di Arafah</h2>
            <ul>
                @foreach (preg_split('/\\r?\\n|•/', trim((string) $cerita->prestasi)) as $poin)
                    @if (trim($poin) !== '')
                        <li>{{ trim($poin) }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ak-kotak ak-cerita">
        <h2>Cerita &amp; pesan untuk adik kelas</h2>
        @forelse ($cerita->bagian_cerita as $paragraf)
            <p>{{ $paragraf }}</p>
        @empty
            <p class="muted kecil">—</p>
        @endforelse
    </div>

    <div class="ak-sisi">
        <h2>Profil singkat</h2>
        <div class="ak-sisikan">
            <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1f3a5f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10L12 5 2 10l10 5 10-5v6"/><path d="M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg></span>
            <span>{{ $cerita->kelas_akhir ?: 'Alumni' }} @if ($cerita->tahun_lulus)· angkatan {{ $cerita->tahun_lulus }}@endif @if ($cerita->status_rangkum)· {{ $cerita->status_rangkum }}@endif</span>
        </div>
        @if ($cerita->instagram_link || $cerita->instagram)
            <a class="ak-ig" href="{{ $cerita->instagram_link ?: ('https://instagram.com/' . trim($cerita->instagram, '@')) }}" target="_blank" rel="noopener">
                Instagram {{ $cerita->instagram }}
            </a>
        @endif
    </div>

    <h2 style="font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#8a94a4;margin:24px 0 4px">Cerita alumni lainnya</h2>
    <div class="ak-lain">
        @foreach ($lainnya as $l)
            <a class="al-lain-kartu" href="{{ url($l->url_halaman) }}">
                <img src="{{ $l->foto_url }}" alt="{{ $l->nama }}" loading="lazy">
                <b>{{ $l->nama }}</b>
                <small>{{ $l->tahun_lulus }}</small>
            </a>
        @endforeach
    </div>
</div>
@endsection
