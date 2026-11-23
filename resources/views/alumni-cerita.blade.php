@extends('layouts.publik')

@section('judul', 'Cerita Alumni')

@section('isi')
<section class="bg-navy-soft py-10">
    <div class="wrap">
        <h1 class="text-navy" style="font-weight:800;font-size:1.7rem">Cerita Alumni</h1>
        <p class="text-putih-70" style="color:#4b5563">Pengalaman dan pesan para alumni untuk adik-adik kelas.</p>
        <form method="GET" class="flex" style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
            <select name="angkatan" style="max-width:180px">
                <option value="">Semua angkatan</option>
                @foreach ($angkatan as $a)
                    <option value="{{ $a }}" @selected(request('angkatan') === $a)>{{ $a }}</option>
                @endforeach
            </select>
            <button class="btn btn-utama btn-kecil" type="submit">Saring</button>
        </form>
    </div>
</section>

<div class="wrap" style="padding:24px 0">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px">
        @forelse ($alumni as $a)
            <article class="kartu" style="padding:16px">
                <img src="{{ $a->foto_url }}" alt="{{ $a->nama }}" loading="lazy"
                     style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:12px;margin-bottom:10px">
                <h3 style="font-weight:700;font-size:15px">{{ $a->nama }}</h3>
                @if ($a->tahun_lulus)<span class="chip">Lulus {{ $a->tahun_lulus }}</span>@endif
                @if ($a->kelas_akhir)<span class="chip">{{ $a->kelas_akhir }}</span>@endif
                @if ($a->status_rangkum)
                    <p class="kecil" style="margin-top:6px"><b>{{ $a->status_rangkum }}</b></p>
                @endif
                @if ($a->pesan)
                    <p class="kecil muted" style="margin-top:8px">{{ \Illuminate\Support\Str::limit($a->pesan, 140) }}</p>
                @endif
                @if ($a->instagram)
                    <a class="tautan-kecil" href="https://instagram.com/{{ trim($a->instagram, '@') }}" target="_blank" rel="noopener" style="color:#9c2f4f;font-weight:700;font-size:12.5px">Instagram ↗</a>
                @endif
            </article>
        @empty
            <p class="muted">Belum ada cerita alumni yang tampil. Isian baru menunggu verifikasi admin.</p>
        @endforelse
    </div>
    <div style="margin-top:18px">{{ $alumni->links() }}</div>
</div>
@endsection
