<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateController extends Controller
{
    /**
     * Template CSV data kelulusan — diunduh admin dari panel, diisi Excel/Spreadsheet,
     * lalu diunggah kembali lewat tombol "Impor CSV".
     */
    public function kelulusan(): StreamedResponse
    {
        $judul = ['nisn', 'nama', 'kelas', 'status', 'pesan', 'tahun_ajaran'];
        $contoh = [
            ['0012345678', 'CONTOH — ganti dengan nama siswa', 'XII IPA 1', 'lulus', 'Barakallahu fiik, jaga nama baik almamater', '2025/2026'],
            ['0012345679', 'CONTOH — baris ini akan dilewati saat impor', 'XII IPS 1', 'lulus', '', '2025/2026'],
        ];

        return response()->streamDownload(function () use ($judul, $contoh) {
            $keluaran = fopen('php://output', 'w');
            fwrite($keluaran, "\xEF\xBB\xBF");            // BOM agar Excel membaca UTF-8 dengan benar
            fputcsv($keluaran, $judul, ',', '"', '\\');
            foreach ($contoh as $baris) {
                fputcsv($keluaran, $baris, ',', '"', '\\');
            }
            fclose($keluaran);
        }, 'template-kelulusan.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
