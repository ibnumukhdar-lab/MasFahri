<div class="text-sm leading-relaxed">
  <p class="mb-2">Bukti pembayaran formulir (Rp 200.000):</p>

  @if ($record->bukti_transfer)
    <p class="mb-1">
      <a href="{{ $record->bukti_transfer_url }}" target="_blank" rel="noopener"
         class="font-semibold underline text-primary-600 dark:text-primary-400">Lihat unggahan bukti pembayaran</a>
    </p>
    <p class="text-xs text-gray-500 dark:text-gray-400">Terbuka di tab baru — periksa nominal dan rekening tujuan.</p>
  @else
    <p class="text-gray-500 dark:text-gray-400">Belum ada bukti transfer yang diunggah — pendaftaran bulan Desember gratis, jadi boleh diverifikasi tanpa bukti.</p>
  @endif
</div>
