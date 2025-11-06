@props(['author', 'editorial' => false])

<article class="ml-summary">
    <a
            @if ($author->defaultUrl?->slug)
                href="{{ route($editorial ? 'trafikrak.storefront.editorial.authors.show' : 'trafikrak.storefront.authors.show', $author->defaultUrl->slug) }}"
            @endif
            class="flex gap-3"
    >
        <div class="summary-image w-1/3">
            <img
                    src="{{ $author->getFirstMediaUrl(config('lunar.media.collection'), 'small') }}"
                    alt=""
                    loading="lazy"
                    class="size-20 min-w-20 max-h-20 rounded-full object-cover object-center"
            >
        </div>
        <div class="summary-content">
            <h2 class="at-heading">
                {{ $author->name }}
            </h2>
        </div>
    </a>
</article>