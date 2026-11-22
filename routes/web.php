<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// Semua fitur bisa diakses tanpa login (sesuai PRD).
// Tamu memakai "mode tamu": catatannya ditandai token anonim di cookie,
// dan otomatis dipindahkan ke akun saat ia daftar/masuk.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Mood Tracker
Route::get('/mood', [MoodController::class, 'index'])->name('mood.index');
Route::post('/mood', [MoodController::class, 'store'])->name('mood.store');
Route::put('/mood/{moodLog}', [MoodController::class, 'update'])->name('mood.update');
Route::delete('/mood/{moodLog}', [MoodController::class, 'destroy'])->name('mood.destroy');

// Jurnal Reflektif
Route::get('/jurnal', [JournalController::class, 'index'])->name('journal.index');
Route::post('/jurnal', [JournalController::class, 'store'])->name('journal.store');
Route::get('/jurnal/export', [JournalController::class, 'exportTxt'])->name('journal.export');
Route::delete('/jurnal/{journalEntry}', [JournalController::class, 'destroy'])->name('journal.destroy');

// Latihan Napas
Route::get('/latihan', [ExerciseController::class, 'index'])->name('exercise.index');
Route::post('/latihan/selesai', [ExerciseController::class, 'complete'])->name('exercise.complete');

// Perpustakaan & Bantuan
Route::get('/jelajahi', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/jelajahi/{slug}', [ResourceController::class, 'show'])->name('resource.show');

// Halaman informasi (footer & tombol bantuan)
Route::get('/tentang', [PageController::class, 'about'])->name('page.about');
Route::get('/privasi', [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/bantuan', [PageController::class, 'help'])->name('page.help');

// Akun opsional (untuk menyimpan catatan lintas perangkat)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
