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
