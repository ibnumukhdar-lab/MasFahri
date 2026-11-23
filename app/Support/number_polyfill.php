<?php
/* Polyfill Number::format untuk server tanpa intl — dipasang 17 Nov 2026.
   Diaktifkan oleh patch kecil di vendor Number.php (dibuat composer-aman). */
if (! function_exists('smaita_number_format')) {
    function smaita_number_format(int|float $angka, ?int $presisi = null, ?int $maksPresisi = null, ?string $locale = null): string
    {
        if ($maksPresisi !== null) {
            $presisi = $maksPresisi; // pembulatan memakai maks presisi
        }
        $hasil = number_format((float) $angka, $presisi ?? 0, ',', '.');
        return $hasil;
    }
}
if (! function_exists('smaita_number_parse')) {
    function smaita_number_parse(string $teks, $jenis = null, ?string $locale = null)
    {
        $bersih = str_replace(['.', ' '], ['', ''], $teks);
        $bersih = str_replace(',', '.', $bersih);
        return is_numeric($bersih) ? (float) $bersih : false;
    }
}