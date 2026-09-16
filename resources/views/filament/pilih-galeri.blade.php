@php
    use App\Support\PustakaMedia;
    $sekarang = data_get($this ?? null, 'data.gambar_sampul');
@endphp
{{-- Isi popup "Pilih dari galeri". Dibungkus x-data supaya magic $wire tersedia. --}}
<div x-data class="space-y-4">
  <p class="text-sm text-gray-500">
    Klik gambar untuk memakainya sebagai gambar sampul. Semua foto di sini berasal dari galeri —
    tambah/rapikan di menu <strong>Galeri</strong>.
  </p>

  @if ($foto->isEmpty())
    <p class="text-sm text-gray-500">Galeri masih kosong. Unggah gambar pada kolom gambar sampul atau lewat menu Galeri.</p>
  @else
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
      @foreach ($foto as $f)
        <button type="button"
                class="group relative overflow-hidden rounded-xl border border-gray-200 text-left hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                x-on:click="$wire.set(@js($target ?? 'data.pilih_galeri'), @js($f->berkas)).then(() => $wire.unmountAction())">
          <img src="{{ $f->url }}" alt="{{ $f->judul }}" class="h-28 w-full object-cover" loading="lazy">
          <span class="absolute inset-x-0 bottom-0 truncate bg-black/55 px-2 py-1 text-[11px] text-white">
            {{ $f->judul ?: basename($f->berkas) }}
          </span>
        </button>
      @endforeach
    </div>
  @endif
</div>
