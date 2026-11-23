@extends('layouts.kelola')

@section('judul', 'Cerita Alumni')

@section('isi')
<h1 style="font-size:22px;margin-bottom:2px">Cerita alumni</h1>
<p class="kecil muted" style="margin-bottom:12px">
    Menunggu verifikasi: {{ $hitung['menunggu'] }} · Tampil: {{ $hitung['tampil'] }} · Total: {{ $hitung['semua'] }}
</p>

<div class="penyaring" style="padding:0 0 12px">
    <a class="chip {{ $status === 'menunggu' ? 'aktif' : '' }}" href="{{ route('kelola.alumni-cerita') }}">Menunggu ({{ $hitung['menunggu'] }})</a>
    <a class="chip {{ $status === 'tampil' ? 'aktif' : '' }}" href="{{ route('kelola.alumni-cerita', ['status' => 'tampil']) }}">Tampil ({{ $hitung['tampil'] }})</a>
    <a class="chip {{ $status === 'semua' ? 'aktif' : '' }}" href="{{ route('kelola.alumni-cerita', ['status' => 'semua']) }}">Semua</a>
</div>

<div class="tabel-bungkus">
    <div class="tabel-gulir">
        <table>
            <thead><tr><th></th><th>Nama</th><th>Angkatan</th><th>Status</th><th>Pesan</th><th></th></tr></thead>
            <tbody>
            @forelse ($alumni as $a)
                <tr>
                    <td><img class="gambar-mini" src="{{ $a->foto_url }}" alt=""></td>
                    <td><b>{{ $a->nama }}</b><div class="kecil muted">{{ $a->wa }}</div></td>
                    <td class="kecil">{{ $a->tahun_lulus ?: '—' }}<div class="kecil muted">{{ $a->kelas_akhir }}</div></td>
                    <td>
                        <span class="lencana {{ $a->tampil ? 'selesai' : 'baru' }}">{{ $a->tampil ? 'Tampil' : 'Menunggu' }}</span>
                        @if ($a->status_saat_ini)<div class="kecil muted">{{ $a->status_label }}</div>@endif
                    </td>
                    <td class="kecil" style="max-width:260px">{{ \Illuminate\Support\Str::limit($a->pesan, 90) }}</td>
                    <td>
                        <div class="aksi-tabel">
                            @if ($a->tampil)
                                <form method="POST" action="{{ route('kelola.alumni-cerita.sembunyikan', $a) }}">
                                    @csrf
                                    <button class="btn btn-garis btn-kecil" type="submit">Sembunyikan</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('kelola.alumni-cerita.setujui', $a) }}">
                                    @csrf
                                    <button class="btn btn-utama btn-kecil" type="submit">Setujui</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('kelola.alumni-cerita.hapus', $a) }}"
                                  onsubmit="return confirm('Hapus isian {{ $a->nama }} beserta fotonya?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-kecil" style="background:#fdecef;color:#96324a;border:0" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="kecil muted">Belum ada isian pada saringan ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="paginasi">{{ $alumni->links() }}</div>
@endsection
