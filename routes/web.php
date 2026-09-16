<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelulusanController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SpmbController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

// ---------- halaman utama ----------
Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/berita', [PublicController::class, 'berita'])->name('berita');
Route::get('/berita/{post:slug}', [PublicController::class, 'beritaShow'])->name('berita.show');
Route::get('/galeri', [PublicController::class, 'galeri'])->name('galeri');
Route::get('/toolsguru', [PublicController::class, 'toolsguru'])->name('toolsguru');
Route::get('/ekskul', [PublicController::class, 'ekskul'])->name('ekskul');
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
Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni');
Route::post('/alumni', [AlumniController::class, 'simpan'])->middleware('throttle:6,1')->name('alumni.simpan');
Route::get('/login', [AuthController::class, 'form'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'masuk'])->middleware('throttle:8,1')->name('login.proses');
Route::post('/logout', [AuthController::class, 'keluar'])->middleware('auth')->name('logout');
Route::get('/profil', [AuthController::class, 'profil'])->middleware('auth')->name('profil');

// ---------- template unduhan untuk admin ----------
Route::get('/template/kelulusan.csv', [TemplateController::class, 'kelulusan'])
    ->middleware('auth')->name('template.kelulusan');

// ---------- alamat lama WordPress: dialihkan permanen (301) ----------
foreach (config('smaita.alihkan', []) as $lama => $tujuan) {
    Route::permanentRedirect('/' . $lama, $tujuan);
}

// ---------- halaman statis (paling akhir, agar tidak menelan rute lain) ----------
Route::get('/{slug}', [PublicController::class, 'halaman'])->name('halaman');
