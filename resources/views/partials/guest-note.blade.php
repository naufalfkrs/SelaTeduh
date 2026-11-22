{{-- Penanda mode tamu: fitur tetap bisa dipakai tanpa login (sesuai PRD) --}}
@guest
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-tertiary-soft px-4 py-3 text-xs leading-relaxed text-tertiary-dark">
        <p class="flex items-start gap-2">
            <x-icon name="user" class="mt-0.5 h-3.5 w-3.5 shrink-0" />
            <span><strong>Mode Tamu aktif.</strong> Kamu bisa langsung mencatat tanpa akun. Catatanmu terhubung ke peramban ini saja; jika cookie dihapus, catatan tamu tidak bisa dibuka lagi.</span>
        </p>
        <span class="flex shrink-0 items-center gap-3 font-semibold">
            <a href="{{ route('register') }}" class="underline underline-offset-4 hover:text-ink">Buat akun</a>
            <a href="{{ route('login') }}" class="underline underline-offset-4 hover:text-ink">Masuk</a>
        </span>
    </div>
@endguest
