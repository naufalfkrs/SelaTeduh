<article class="card group flex flex-col p-5 transition hover:-translate-y-0.5 hover:shadow-[0_16px_40px_-28px_rgba(41,47,42,0.45)]">
    <div class="flex items-center justify-between gap-2 text-[11px]">
        <span class="chip-soft py-0.5 text-[11px]">{{ $article->category }}</span>
        <span class="flex items-center gap-1 text-subtle"><x-icon name="clock" class="h-3 w-3" /> {{ $article->read_time }} mnt baca</span>
    </div>

    <h3 class="mt-4 font-serif text-xl leading-snug text-ink">
        <a href="{{ route('resource.show', $article->slug) }}" class="group-hover:text-primary">{{ $article->title }}</a>
    </h3>

    <p class="mt-2 mb-5 line-clamp-3 grow text-[13px] leading-relaxed text-muted">{{ $article->summary }}</p>

    <div class="flex items-center justify-between gap-3 border-t border-line pt-4 text-[11px] text-subtle">
        <span class="truncate">{{ $article->reviewer ?? 'Ditinjau Tim SelaTeduh' }}</span>
        <a href="{{ route('resource.show', $article->slug) }}" class="shrink-0 text-muted hover:text-primary" aria-label="Baca {{ $article->title }}">
            <x-icon name="bookmark" class="h-4 w-4" />
        </a>
    </div>
</article>
