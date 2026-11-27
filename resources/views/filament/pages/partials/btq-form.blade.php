@php $aspek = \App\Models\SpmbNilaiQuran::ASPEK; @endphp
<div class="rp-form">
  <p class="rp-form-judul">Penilaian BTQ — {{ $r->nama_siswa }}</p>

  @if ($errors->any())
    <div class="rp-error">Periksa isian: {{ implode(' · ', $errors->all()) }}</div>
  @endif

  <div class="rp-form-grid">
    @foreach ($aspek as $a)
      <div class="rp-form-grup">
        <p class="rp-form-nama">{{ $a['label'] }}</p>
        @foreach ($a['kriteria'] as $kolom => $label)
          <label class="rp-form-item">
            <span>{{ $label }}</span>
            <input class="rp-input" type="number" min="0" max="100" inputmode="numeric"
                   wire:model="form.{{ $kolom }}" placeholder="—">
          </label>
        @endforeach
      </div>
    @endforeach
  </div>

  <div class="rp-form-bawah">
    <input class="rp-input-lebar" wire:model="form.penilai" placeholder="Nama penilai (opsional)">
    <input class="rp-input-lebar" wire:model="form.catatan" placeholder="Catatan (opsional)">
    <button type="button" class="rp-btn simpan" wire:click="simpanBtqForm">Simpan nilai</button>
    <button type="button" class="rp-btn batal" wire:click="tutupBtq">Batal</button>
  </div>
</div>
