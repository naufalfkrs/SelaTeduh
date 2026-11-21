<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Filter query otomatis milik user yang sedang login.
     */
    protected function scopeOwner(Builder $query): Builder
    {
        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }

        // Jika tamu membuka Beranda, kembalikan data kosong dengan aman
        return $query->whereRaw('1 = 0');
    }

    /**
     * Atribut kepemilikan saat menyimpan data baru.
     */
    protected function ownerAttributes(): array
    {
        return ['user_id' => Auth::id()];
    }

    /**
     * Validasi hak akses agar user tidak bisa mengubah/hapus data milik orang lain.
     */
    protected function authorizeOwnership($model): void
    {
        abort_unless($model->user_id === Auth::id(), 403, 'Akses tidak diizinkan.');
    }
}