@extends('layouts.app')

@section('title', 'Buat Ruang Teduhmu — SelaTeduh')

@section('auth-card')
    <span class="chip-soft"><x-icon name="sprout" class="h-3 w-3" /> Mulai Perlahan</span>
    <h1 class="heading-lg mt-4">Buat ruang teduhmu.</h1>
    <p class="mt-2 text-sm text-muted">Akun bersifat opsional: semua fitur tetap bisa dipakai tanpa daftar. Dengan akun, catatan bisa dibuka lintas perangkat dan catatan tamumu ikut dipindahkan.</p>

    <form method="POST" action="{{ route('register.post') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="name" class="mb-1.5 block text-xs font-semibold text-ink">Nama Panggilan</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="nickname"
                   placeholder="Bagaimana kami menyapamu?" class="field bg-white">
            @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-xs font-semibold text-ink">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                   placeholder="nama@email.com" class="field bg-white">
            @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-1.5 block text-xs font-semibold text-ink">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       placeholder="Min. 8 karakter" class="field bg-white">
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold text-ink">Ulangi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       placeholder="••••••••" class="field bg-white">
            </div>
        </div>
        @error('password') <p class="-mt-2 text-xs text-danger">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary w-full py-3">Buat Akun</button>
    </form>

    <p class="mt-6 text-center text-xs text-muted">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-primary underline underline-offset-2">Masuk di sini</a>
    </p>
@endsection

@section('content')
    @include('auth.partials.shell')
@endsection
