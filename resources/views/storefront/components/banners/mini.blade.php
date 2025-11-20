<article class="bg-secondary p-6 border">
    <h2 class="at-heading is-2 mb-2">{{ $banner->name }}</h2>

    @if ($banner->description)
        <div class="mb-4">
            {!! $banner->description !!}
        </div>
    @endif

    @if ($banner->button_text && $banner->link)
        <a href="{{ $banner->link }}" class="at-button is-primary">
            {{ $banner->button_text }}
        </a>
    @endif
</article>