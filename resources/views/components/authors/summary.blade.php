@props(['author', 'editorial' => false, 'showImage' => true])

@php
    $hasProfilePage = ($author->defaultUrl?->slug && $author->translateAttribute('has-profile-page'));
@endphp

<article class="ml-summary">
    <a
            @if ($hasProfilePage)
                href="{{ route($editorial ? 'testa.storefront.editorial.authors.show' : 'testa.storefront.authors.show', $author->defaultUrl->slug) }}"
            @endif
            class="flex gap-4"
    >
        @if ($showImage)
            <div class="summary-image">
                <img
                        src="{{ $author->getFirstMediaUrl(config('lunar.media.collection'), 'small') }}"
                        alt=""
                        loading="lazy"
                        class="size-30 min-w-30 max-h-30 rounded-full object-cover object-center"
                >
            </div>
        @endif
        <div class="summary-content">
            <h2 class="at-heading {{ ! $hasProfilePage ? 'text-black' : '' }}">
                {{ $author->name }}
            </h2>

            @if ($author->translateAttribute('biography'))
                <div class="mt-2 text-black">
                    {!! \Illuminate\Support\Str::limit($author->translateAttribute('biography'), 120) !!}
                </div>
            @endif
        </div>
    </a>
</article>