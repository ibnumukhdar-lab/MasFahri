<?php

use App\Http\Controllers\SpmbPendaftarController;
use Illuminate\Support\Facades\Route;

// ---------- area pendaftar SPMB (login nomor pendaftaran + WA) ----------
Route::get('/spmb/masuk', [SpmbPendaftarController::class, 'formLogin'])->name('spmb.pendaftar.masuk');
Route::post('/spmb/masuk', [SpmbPendaftarController::class, 'login'])->middleware('throttle:8,1')->name('spmb.pendaftar.login');
Route::post('/spmb/keluar', [SpmbPendaftarController::class, 'logout'])->name('spmb.pendaftar.keluar');
Route::get('/spmb/kena', [SpmbPendaftarController::class, 'belumVerifikasi'])->name('spmb.pendaftar.kena');
Route::get('/spmb/dasbor', [SpmbPendaftarController::class, 'dasbor'])->name('spmb.pendaftar.dasbor');
Route::post('/spmb/dasbor/biodata', [SpmbPendaftarController::class, 'simpanBiodata'])->middleware('throttle:10,1')->name('spmb.pendaftar.biodata');

// ---------- kartu tes & CBT (gerbang kode tes) ----------
Route::get('/spmb/kartu', [SpmbPendaftarController::class, 'kartu'])->name('spmb.pendaftar.kartu');
Route::get('/spmb/tes', [SpmbPendaftarController::class, 'tes'])->name('spmb.pendaftar.tes');
Route::post('/spmb/tes/mulai', [SpmbPendaftarController::class, 'tesMulai'])->middleware('throttle:10,1')->name('spmb.pendaftar.tes.mulai');
Route::get('/spmb/tes/kerjakan', [SpmbPendaftarController::class, 'kerjakan'])->name('spmb.pendaftar.kerjakan');
Route::post('/spmb/tes/simpan', [SpmbPendaftarController::class, 'simpanTes'])->middleware('throttle:20,1')->name('spmb.pendaftar.tes.simpan');
