{{-- Kerangka dua kolom untuk halaman Masuk & Daftar --}}
<div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
    <aside class="relative hidden overflow-hidden bg-primary lg:block">
        <img src="https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=1200&q=80"
             alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
        <div class="relative flex h-full flex-col justify-between p-12 text-white">
            <a href="{{ route('home') }}" class="flex items-center gap-1.5 font-serif text-2xl">
                <span class="h-1.5 w-1.5 rounded-full bg-white"></span> SelaTeduh
            </a>
            <div>
                <p class="max-w-md font-serif text-4xl leading-tight">"Tak apa, ambil jeda. Kamu nggak harus baik-baik saja setiap saat."</p>
                <p class="mt-4 max-w-sm text-sm text-white/75">Ruang teduh digital untuk bernapas, mengenali rasa, dan merawat diri tanpa tuntutan sempurna.</p>
            </div>
            <p class="flex items-center gap-2 text-xs text-white/60"><x-icon name="lock" class="h-3.5 w-3.5" /> Catatanmu privat dan hanya untuk dirimu.</p>
        </div>
    </aside>

    <div class="flex items-center justify-center px-4 py-12 sm:px-8">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 flex w-fit items-center gap-1.5 text-xs font-medium text-muted hover:text-primary">
                <x-icon name="arrow-left" class="h-3.5 w-3.5" /> Kembali ke Beranda
            </a>
            @yield('auth-card')
        </div>
    </div>
</div>
