<?php

use App\Http\Controllers\AlumniCeritaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SpmbController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

// ---------- area pendaftar SPMB (login nomor + WA) ----------
require __DIR__ . '/spmb-pendaftar.php';

// ---------- halaman utama ----------
Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/berita', [PublicController::class, 'berita'])->name('berita');
Route::get('/berita/{post:slug}', [PublicController::class, 'beritaShow'])->name('berita.show');
Route::get('/galeri', [PublicController::class, 'galeri'])->name('galeri');
Route::get('/toolsguru', [PublicController::class, 'toolsguru'])->name('toolsguru');
Route::get('/ekskul', [PublicController::class, 'ekskul'])->name('ekskul');
Route::get('/ekskul/{ekskul:slug}', [PublicController::class, 'ekskulShow'])->name('ekskul.show');
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PublicController::class, 'robots']);

// ---------- SPMB ----------
Route::get('/spmb', [SpmbController::class, 'index'])->name('spmb');
Route::post('/spmb', [SpmbController::class, 'simpan'])->middleware('throttle:10,1')->name('spmb.simpan');
Route::get('/spmb/status', [SpmbController::class, 'status'])->name('spmb.status');

// ---------- kelulusan ----------
Route::get('/pengumuman-kelulusan', [KelulusanController::class, 'index'])->name('kelulusan');
Route::get('/pengumuman-kelulusan/cek', [KelulusanController::class, 'cek'])->name('kelulusan.cek');

// ---------- alumni & akun ----------
// Direktori alumni lama sudah dilebur ke Cerita Alumni (27 Nov 2026).
Route::get('/alumni', fn () => redirect()->route('alumni.cerita', [], 301))->name('alumni');
Route::get('/login', [AuthController::class, 'form'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'masuk'])->middleware('throttle:8,1')->name('login.proses');
Route::post('/logout', [AuthController::class, 'keluar'])->middleware('auth')->name('logout');
Route::get('/profil', [AuthController::class, 'profil'])->middleware('auth')->name('profil');

// ---------- template unduhan untuk admin ----------
Route::get('/template/kelulusan.csv', [TemplateController::class, 'kelulusan'])
    ->middleware('auth')->name('template.kelulusan');

// ---------- display TV (layar informasi sekolah) ----------
Route::get('/display', [DisplayController::class, 'index'])->name('display');

// ---------- cerita alumni (form publik tanpa akun) ----------
Route::get('/alumni-cerita', [AlumniCeritaController::class, 'index'])->name('alumni.cerita');
Route::get('/alumni-cerita/isi', [AlumniCeritaController::class, 'form'])->name('alumni.cerita.form');
Route::post('/alumni-cerita', [AlumniCeritaController::class, 'simpan'])->name('alumni.cerita.simpan')->middleware('throttle:6,1');
Route::get('/alumni-cerita/sukses/{token}', [AlumniCeritaController::class, 'sukses'])->name('alumni.cerita.sukses');
Route::get('/alumni-cerita/{slug}', [AlumniCeritaController::class, 'show'])->name('alumni.cerita.show');

    Route::get('/alumni-cerita/sunting/{token}', [AlumniCeritaController::class, 'sunting'])->name('alumni.cerita.sunting');
Route::post('/alumni-cerita/sunting/{token}', [AlumniCeritaController::class, 'perbarui'])->name('alumni.cerita.perbarui');
// ---------- alamat lama WordPress: dialihkan permanen (301) ----------
foreach (config('smaita.alihkan', []) as $lama => $tujuan) {
    Route::permanentRedirect('/' . $lama, $tujuan);
}

// ---------- halaman statis (paling akhir, agar tidak menelan rute lain) ----------
Route::get('/{slug}', [PublicController::class, 'halaman'])->name('halaman');
