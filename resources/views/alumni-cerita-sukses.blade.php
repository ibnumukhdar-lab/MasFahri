@extends('layouts.publik')

@section('judul', 'Cerita Terkirim')

@section('isi')
<div class="wrap" style="max-width:560px;padding:40px 0;text-align:center">
    <div style="width:56px;height:56px;border-radius:50%;background:#e8f3ec;color:#256248;display:grid;place-items:center;margin:0 auto 12px;font-size:26px">✓</div>
    <h1 style="font-weight:800;font-size:1.6rem;color:#1f3a5f">Ceritamu terkirim</h1>
    <p class="muted kecil">Admin akan memeriksa, lalu ceritamu tampil di halaman Alumni.</p>

    <div class="kartu" style="padding:14px;text-align:left;margin-top:14px">
        <b>{{ $cerita->nama }}</b>
        @if ($cerita->tahun_lulus)<span class="chip">Lulus {{ $cerita->tahun_lulus }}</span>@endif
        @if ($cerita->pesan)<p class="kecil muted" style="margin-top:6px">{{ \Illuminate\Support\Str::limit($cerita->pesan, 180) }}</p>@endif
    </div>

    <div class="kartu" style="padding:14px;text-align:left;margin-top:12px;border-left:4px solid #9c2f4f">
        <b style="font-size:13px">Simpan tautan ini</b>
        <p class="kecil muted">Kalau mau mengubah cerita atau fotomu, buka tautan berikut:</p>
        <p style="word-break:break-all;font-size:12px"><a href="{{ route('alumni.cerita.sunting', $cerita->token) }}" style="color:#9c2f4f;font-weight:700">{{ route('alumni.cerita.sunting', $cerita->token) }}</a></p>
    </div>

    <a class="btn btn-garis btn-blok" href="{{ route('alumni.cerita') }}" style="margin-top:14px">Lihat halaman Alumni</a>
</div>
@endsection
