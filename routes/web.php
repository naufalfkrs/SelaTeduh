<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// Halaman Publik (Bisa diakses tanpa login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/latihan', [ExerciseController::class, 'index'])->name('exercise.index');
Route::get('/jelajahi', [ResourceController::class, 'index'])->name('resource.index');
Route::get('/jelajahi/{slug}', [ResourceController::class, 'show'])->name('resource.show');

// Autentikasi (Khusus pengunjung yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Fitur Privat (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

    // Simpan riwayat sesi latihan napas
    Route::post('/latihan/selesai', [ExerciseController::class, 'complete'])->name('exercise.complete');
});