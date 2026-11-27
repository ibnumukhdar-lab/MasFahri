@props([
    'name',
    'label' => null,
    'value' => null,
    'type' => 'text',
    'note' => null,
    'req' => false,
    'placeholder' => null,
    'maxlength' => null,
    'inputmode' => null,
    'col' => null,
])
<div class="{{ $col ?? '' }}">
  @if ($label)
    <label for="f-{{ $name }}" class="sf-label">
      {{ $label }}<span class="sf-wajib" title="Wajib diisi saat menyerahkan">&bull;</span>
      @if ($note)<span class="sf-note">{{ $note }}</span>@endif
    </label>
  @endif
  <input id="f-{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
    @if ($req) required @endif
    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
    @if ($inputmode) inputmode="{{ $inputmode }}" @endif
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'sf-input']) }}>
</div>
