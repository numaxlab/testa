<div class="container mx-auto px-4 mb-9">
    <article
            class="min-h-90 lg:flex {{ $banner->style === 'positive' ? 'bg-secondary text-black' : 'bg-accent text-white' }}"
    >
        @if ($banner->hasMedia(config('lunar.media.collection')))
            <div class="lg:w-1/2">
                <img src="{{ $banner->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt=""
                     class="w-full h-full object-cover">
            </div>
        @endif
        <div class="p-8 lg:w-1/2 lg:py-8 lg:pr-20 lg:pl-8">
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
    </article>
</div>
