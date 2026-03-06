<article
        class="relative w-full overflow-hidden min-h-90 mb-9 {{ $banner->style === 'positive' ? 'bg-secondary text-black' : 'bg-accent text-white' }}"
>
    @if ($banner->hasMedia(config('lunar.media.collection')))
        <div class="w-full lg:absolute lg:top-0 lg:bottom-0 lg:right-0 lg:w-1/2">
            <img src="{{ $banner->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt=""
                 class="w-full h-full object-cover">
        </div>
    @endif

    <div class="relative container mx-auto px-4">
        <div class="w-full p-8 lg:w-1/2 lg:py-8 lg:pr-20 lg:pl-0">
            <h2 class="at-heading is-2 mb-2">{{ $banner->name }}</h2>

            @if ($banner->description)
                <div class="{{ $banner->style === 'positive' ? 'prose' : 'prose-invert' }}">
                    {!! $banner->description !!}
                </div>
            @endif

            @if ($banner->button_text && $banner->link)
                <a href="{{ $banner->link }}" class="at-button is-primary inline-block mt-5">
                    {{ $banner->button_text }}
                </a>
            @endif
        </div>
    </div>
</article>
