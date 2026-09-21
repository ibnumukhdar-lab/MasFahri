<?php

namespace App\Support;

use App\Models\BerandaBagian;
use App\Models\Page;
use DOMDocument;

/**
 * Menyelaraskan isi bagian beranda dengan HALAMAN yang sejenis
 * (visi-misi, tentang, kurikulum). Halaman = sumber kebenaran, beranda mengikuti.
 *
 * Dipakai oleh: tombol "Selaraskan dari halaman" di panel + skrip pemasangan.
 */
class SelaraskanBeranda
{
    /** Urutan elemen teks dalam isi halaman: judul (h2/h3), paragraf (p), butir (li). */
    public static function urut(string $html): array
    {
        $dom = new DOMDocument();
        $sebelum = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($sebelum);

        $hasil = [];
        foreach ($dom->getElementsByTagName('*') as $simpul) {
            $tag = strtolower($simpul->nodeName);
            if (! in_array($tag, ['h2', 'h3', 'p', 'li'], true)) {
                continue;
            }
            $teks = trim((string) preg_replace('~\s+~u', ' ', $simpul->textContent));
            if ($teks === '') {
                continue;
            }
            $hasil[] = ['tag' => $tag, 'teks' => $teks];
        }

        return $hasil;
    }

    private static function isiHalaman(string $slug): ?string
    {
        $halaman = Page::terbit()->where('slug', $slug)->first();

        return $halaman ? (string) $halaman->body : null;
    }

    /**
     * Jalankan penyelarasan. Mengembalikan laporan per bagian.
     */
    public static function jalan(): array
    {
        $lapor = [];

        // ---------- VISI & MISI : visi + tiap misi jadi satu butir ----------
        if ($body = self::isiHalaman('visi')) {
            $urut = self::urut($body);
            $visi = null;
            foreach ($urut as $i => $satu) {
                if (mb_strtoupper($satu['teks']) === 'VISI' && isset($urut[$i + 1])) {
                    $visi = $urut[$i + 1]['teks'];
                    break;
                }
            }
            $misi = [];
            foreach ($urut as $satu) {
                if ($satu['tag'] === 'li') {
                    $misi[] = ['teks' => $satu['teks']];
                }
            }
            $bagian = BerandaBagian::query()->where('jenis', 'visi_misi')->first();
            if ($bagian) {
                if ($visi) {
                    $bagian->teks = $visi;
                }
                if ($misi) {
                    $bagian->data = $misi;
                }
                $bagian->save();
                $lapor['visi_misi'] = ($visi ? 'visi disalin, ' : '').count($misi).' misi disalin';
            }
        }

        // ---------- TENTANG : seluruh paragraf halaman (tanpa terpotong) ----------
        if ($body = self::isiHalaman('tentang-sma-it-arafah')) {
            $paragraf = [];
            foreach (self::urut($body) as $satu) {
                if ($satu['tag'] === 'p') {
                    $paragraf[] = $satu['teks'];
                }
            }
            $bagian = BerandaBagian::query()->where('jenis', 'tentang')->first();
            if ($bagian && $paragraf) {
                $bagian->teks = implode("\n\n", $paragraf);
                $bagian->save();
                $lapor['tentang'] = count($paragraf).' paragraf disalin';
            }
        }

        // ---------- KURIKULUM : paragraf pembuka + tiap judul/paragraf jadi kartu ----------
        if ($body = self::isiHalaman('kurikulum')) {
            $urut = self::urut($body);
            $pembuka = [];
            $kartu = [];
            $judulKartu = null;
            $teksKartu = [];
            foreach ($urut as $satu) {
                if ($satu['tag'] === 'h2') {
                    continue;
                }
                if ($satu['tag'] === 'h3') {
                    if ($judulKartu !== null) {
                        $kartu[] = ['judul' => $judulKartu, 'teks' => implode(' ', $teksKartu)];
                    }
                    $judulKartu = $satu['teks'];
                    $teksKartu = [];
                    continue;
                }
                if ($satu['tag'] !== 'p') {
                    continue;
                }
                if ($judulKartu === null) {
                    $pembuka[] = $satu['teks'];
                } else {
                    $teksKartu[] = $satu['teks'];
                }
            }
            if ($judulKartu !== null) {
                $kartu[] = ['judul' => $judulKartu, 'teks' => implode(' ', $teksKartu)];
            }

            $bagian = BerandaBagian::query()->where('jenis', 'kurikulum')->first();
            if ($bagian) {
                if ($pembuka) {
                    $bagian->subjudul = implode("\n\n", $pembuka);
                }
                if ($kartu) {
                    $bagian->data = $kartu;
                }
                $bagian->save();
                $lapor['kurikulum'] = ($pembuka ? 'pembuka disalin, ' : '').count($kartu).' kartu disalin';
            }
        }

        return $lapor;
    }
}
