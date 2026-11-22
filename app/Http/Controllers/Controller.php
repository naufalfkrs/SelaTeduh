<?php

namespace App\Http\Controllers;

use App\Models\BreathingLog;
use App\Models\JournalEntry;
use App\Models\MoodLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Token anonim pengunjung (lihat middleware EnsureGuestToken).
     */
    protected function guestToken(): ?string
    {
        return request()->attributes->get('guest_token');
    }

    /**
     * Filter query hanya data milik pemiliknya:
     * user yang login, atau tamu berdasarkan token anonimnya.
     */
    protected function scopeOwner(Builder $query): Builder
    {
        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }

        if ($token = $this->guestToken()) {
            return $query->whereNull('user_id')->where('guest_token', $token);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Atribut kepemilikan saat menyimpan data baru.
     */
    protected function ownerAttributes(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id(), 'guest_token' => null];
        }

        return ['user_id' => null, 'guest_token' => $this->guestToken()];
    }

    /**
     * Pastikan data yang diubah/dihapus benar milik pengunjung ini.
     */
    protected function authorizeOwnership($model): void
    {
        $allowed = Auth::check()
            ? (int) $model->user_id === (int) Auth::id()
            : $model->user_id === null
                && is_string($model->guest_token)
                && is_string($this->guestToken())
                && hash_equals($model->guest_token, $this->guestToken());

        abort_unless($allowed, 403, 'Akses tidak diizinkan.');
    }

    /**
     * Saat tamu login/daftar, pindahkan catatan tamunya ke akun tersebut.
     */
    protected function claimGuestData(int $userId): void
    {
        $token = $this->guestToken();
        if (! $token) {
            return;
        }

        foreach ([MoodLog::class, JournalEntry::class, BreathingLog::class] as $model) {
            $model::query()
                ->whereNull('user_id')
                ->where('guest_token', $token)
                ->update(['user_id' => $userId, 'guest_token' => null]);
        }
    }
}
