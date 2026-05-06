<article {{ $attributes->merge(['class' => 'ml-summary flex gap-3 border-b'])->filter(fn ($value, $key) => ! in_array($key, ['href'])) }}>
    <a href="{{ Storage::url($media->path) }}" class="w-1/3 aspect-[2/3] shrink-0 self-start mb-4">
        <span class="sr-only">{{ __('Descargar') }} {{ $media->name }}</span>
        <div class="summary-media-wrapper flex items-center justify-center bg-neutral-100 w-full h-full">
            <div class="flex flex-col items-center gap-2 text-neutral-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
                <span class="text-xs font-semibold uppercase tracking-widest">{{ strtoupper(pathinfo($media->path, PATHINFO_EXTENSION)) }}</span>
            </div>
        </div>
    </a>
    <div class="w-2/3 pr-5">
        <a href="{{ Storage::url($media->path) }}" target="_blank">
            <h3 class="at-heading is-3">
                {{ $media->name }}
            </h3>
        </a>

        @if ($media->is_private)
            <div class="mt-2">
                <span class="at-tag is-primary text-sm">{{ __('Recurso privado') }}</span>
            </div>
        @endif

        @if ($media->description)
            <div class="summary-content">
                {!! $media->description !!}
            </div>
        @endif
    </div>
</article>
