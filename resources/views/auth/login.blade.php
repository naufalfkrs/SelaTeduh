@extends('layouts.app')

@section('title', 'Masuk ke Ruang Teduhmu — SelaTeduh')

@section('auth-card')
    <span class="chip-soft"><x-icon name="lock" class="h-3 w-3" /> Ruang Privatmu</span>
    <h1 class="heading-lg mt-4">Selamat datang kembali.</h1>
    <p class="mt-2 text-sm text-muted">Masuk untuk melanjutkan catatan perasaan dan lembar jurnalmu.</p>

    <form method="POST" action="{{ route('login.post') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-ink">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="nama@email.com" class="field bg-white">
            @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-xs font-semibold text-ink">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••" class="field bg-white">
            @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <label class="inline-flex cursor-pointer items-center gap-2 text-xs text-muted">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-line accent-primary">
            Ingat sesi saya
        </label>

        <button type="submit" class="btn-primary w-full py-3">Masuk ke SelaTeduh</button>
    </form>

    <p class="mt-6 text-center text-xs text-muted">
        Belum punya ruang pribadi?
        <a href="{{ route('register') }}" class="font-semibold text-primary underline underline-offset-2">Buat akun baru</a>
    </p>
@endsection

@section('content')
    @include('auth.partials.shell')
@endsection
