<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SelaTeduh — Tak apa, ambil jeda.')</title>
    <meta name="description" content="SelaTeduh — ruang teduh digital bagi remaja dan anak muda untuk bernapas, mengenali rasa, dan merawat diri tanpa tuntutan sempurna.">

    <!-- Google Fonts: Newsreader (headline) & Plus Jakarta Sans (body/label) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..600;1,6..72,300..500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    @php
        $navItems = [
            ['label' => 'Beranda', 'route' => 'home',           'active' => request()->routeIs('home')],
            ['label' => 'Jelajahi', 'route' => 'resource.index', 'active' => request()->routeIs('resource.*')],
            ['label' => 'Latihan', 'route' => 'exercise.index',  'active' => request()->routeIs('exercise.*')],
            ['label' => 'Jurnal',  'route' => 'journal.index',   'active' => request()->routeIs('journal.*')],
            ['label' => 'Mood',    'route' => 'mood.index',      'active' => request()->routeIs('mood.*')],
        ];
    @endphp

    <!-- Navbar -->
    @unless (request()->routeIs('login', 'register'))
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-line/70 bg-canvas/90 backdrop-blur-md">
        <div class="container-page flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-serif text-[22px] tracking-tight text-primary">
                SelaTeduh
            </a>

            <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi utama">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       @if ($item['active']) aria-current="page" @endif
                       class="rounded-full px-4 py-1.5 text-[13px] font-medium transition {{ $item['active'] ? 'bg-primary text-white' : 'text-muted hover:text-ink hover:bg-secondary' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('page.help') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3.5 py-1.5 text-xs font-medium text-primary transition hover:bg-secondary">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                    Butuh bantuan?
                </a>

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = !open" type="button" aria-label="Menu akun"
                                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-secondary font-serif text-base text-primary ring-1 ring-line transition hover:bg-secondary-dark">
                            {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                        </button>

                        <div x-show="open" x-cloak x-on:click.outside="open = false" x-transition
                             class="absolute right-0 z-50 mt-2 w-52 rounded-2xl border border-line bg-white py-1.5 text-sm shadow-lg">
                            <p class="px-4 pt-1.5 pb-2 text-xs text-subtle">Halo, <span class="font-semibold text-ink">{{ Str::limit(Auth::user()->name, 18) }}</span></p>
                            <a href="{{ route('mood.index') }}" class="flex items-center gap-2 px-4 py-2 text-ink hover:bg-canvas"><x-icon name="calendar-heart" /> Catatan Mood</a>
                            <a href="{{ route('journal.index') }}" class="flex items-center gap-2 px-4 py-2 text-ink hover:bg-canvas"><x-icon name="notebook-pen" /> Jurnal Refleksi</a>
                            <hr class="my-1 border-line">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full cursor-pointer items-center gap-2 px-4 py-2 text-left text-danger hover:bg-red-50">
                                    <x-icon name="log-out" /> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" aria-label="Masuk"
                       class="flex h-9 items-center gap-1.5 rounded-full bg-white px-3 text-xs font-semibold text-primary ring-1 ring-line transition hover:bg-secondary">
                        <x-icon name="user" class="w-4 h-4" />
                        <span class="hidden sm:inline">Masuk</span>
                    </a>
                @endauth

                <button type="button" x-on:click="mobileOpen = !mobileOpen" aria-label="Buka menu"
                        class="md:hidden flex h-9 w-9 cursor-pointer items-center justify-center rounded-full text-primary ring-1 ring-line bg-white">
                    <x-icon name="menu" x-show="!mobileOpen" />
                    <x-icon name="x" x-show="mobileOpen" x-cloak />
                </button>
            </div>
        </div>

        <!-- Menu mobile -->
        <nav x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-line bg-canvas">
            <div class="container-page flex flex-col gap-1 py-3">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="rounded-xl px-4 py-2.5 text-sm font-medium {{ $item['active'] ? 'bg-primary text-white' : 'text-muted hover:bg-secondary' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('page.help') }}" class="rounded-xl px-4 py-2.5 text-sm font-medium text-primary hover:bg-secondary">
                    Butuh bantuan?
                </a>
            </div>
        </nav>
    </header>
    @endunless

    <!-- Notifikasi Flash -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition
             class="container-page mt-4">
            <div class="flex items-center justify-between gap-3 rounded-2xl border border-primary-soft bg-secondary px-4 py-3 text-sm text-primary">
                <span class="flex items-center gap-2"><x-icon name="check" /> {{ session('success') }}</span>
                <button x-on:click="show = false" class="cursor-pointer text-xs opacity-70 hover:opacity-100">Tutup</button>
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @unless (request()->routeIs('login', 'register'))
    <footer class="mt-20 border-t border-line bg-secondary/60">
        <div class="container-page py-12">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-12">
                <div class="col-span-2 md:col-span-4">
                    <span class="font-serif text-[22px] text-primary">SelaTeduh</span>
                    <p class="mt-1 font-serif text-base italic text-muted">Tak apa, ambil jeda.</p>
                    <p class="mt-4 max-w-xs text-[13px] leading-relaxed text-muted">
                        Ruang teduh digital bagi remaja dan anak muda untuk bernapas, mengenali rasa, dan merawat diri tanpa tuntutan sempurna.
                    </p>
                    <p class="mt-4 text-xs text-subtle">© {{ date('Y') }} SelaTeduh. Dibuat dengan keheningan dan perhatian.</p>
                </div>

                @php
                    $footerCols = [
                        // Setiap tautan menuju halaman/bagian yang berbeda dan benar-benar ada
                        'Tentang'    => [['Tentang SelaTeduh', route('page.about')], ['Filosofi Kami', route('page.about').'#filosofi'], ['Nilai Bersama', route('page.about').'#nilai']],
                        'Dukungan'   => [['Panduan Mencari Bantuan', route('page.help')], ['Kontak Darurat', route('page.help').'#darurat'], ['Teman Sebaya', route('page.help').'#sebaya']],
                        'Etika'      => [['Privasi & Etika', route('page.privacy')], ['Keamanan Data', route('page.privacy').'#keamanan'], ['Batasan Layanan', route('page.privacy').'#batasan']],
                        'Ruang Jeda' => [['Latihan Napas', route('exercise.index')], ['Jurnal Refleksi', route('journal.index')], ['Catatan Mood', route('mood.index')], ['Perpustakaan Rasa', route('resource.index')]],
                    ];
                @endphp

                @foreach ($footerCols as $heading => $links)
                    <div class="md:col-span-2">
                        <h4 class="mb-3 text-[11px] font-bold uppercase tracking-[0.12em] text-ink">{{ $heading }}</h4>
                        <ul class="space-y-2 text-[13px] text-muted">
                            @foreach ($links as [$label, $href])
                                <li><a href="{{ $href }}" class="transition hover:text-primary">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 flex gap-3 rounded-2xl border border-line bg-white/70 p-4 text-xs leading-relaxed text-muted">
                <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
                <p>
                    <strong class="block text-ink">Catatan Penting Pendampingan</strong>
                    SelaTeduh dihadirkan sebagai kawan refleksi mandiri dan sarana penenang pikiran, bukan pengganti penanganan medis, diagnosis psikologis, psikiatris, maupun terapi profesional. Jika kamu atau temanmu tengah berada dalam situasi krisis emosional mendesak, silakan segera menghubungi fasilitas kesehatan terdekat atau mengakses daftar
                    <a href="{{ route('page.help') }}#darurat" class="font-semibold text-primary underline underline-offset-2">Kontak Darurat</a> yang tersedia.
                </p>
            </div>
        </div>
    </footer>
    @endunless
</body>
</html>
