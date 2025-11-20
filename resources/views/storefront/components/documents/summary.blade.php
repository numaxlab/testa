<article {{ $attributes->merge(['class' => 'ml-summary flex gap-3 border-b'])->filter(fn ($value, $key) => ! in_array($key, ['href'])) }}>
    <a href="{{ $attributes->get('href') }}" class="w-1/3">
        <div class="summary-media-wrapper" href="{{ $attributes->get('href') }}" target="_blank">
            <img src="https://picsum.photos/600/800" alt="" loading="lazy">
        </div>
    </a>
    <div class="w-2/3 pr-5">
        <a href="{{ $attributes->get('href') }}" target="_blank">
            <h3 class="at-heading is-3">
                {{ $media->name }}
            </h3>
        </a>

        @if ($media->description)
            <div class="summary-content">
                {!! $media->description !!}
            </div>
        @endif
    </div>
</article>
