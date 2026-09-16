<?php

namespace App\Http\Controllers;

use App\Models\Graduate;
use Illuminate\Http\Request;

class KelulusanController extends Controller
{
    public function index()
    {
        return view('kelulusan', [
            'nisn' => null,
            'siswa' => null,
            'adaData' => Graduate::where('tampil', true)->exists(),
        ]);
    }

    public function cek(Request $request)
    {
        $nisn = trim((string) $request->query('nisn'));

        return view('kelulusan', [
            'nisn' => $nisn,
            'siswa' => $nisn !== '' ? Graduate::where('nisn', $nisn)->where('tampil', true)->latest('tahun_ajaran')->first() : null,
            'adaData' => Graduate::where('tampil', true)->exists(),
        ]);
    }
}
