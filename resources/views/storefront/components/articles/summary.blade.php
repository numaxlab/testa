<x-numaxlab-atomic::molecules.summary
        href="{{ route('trafikrak.storefront.articles.show', $article->defaultUrl->slug) }}">
    <x-slot name="thumbnail">
        <img src="{{ Storage::url($article->image) }}" alt="">
    </x-slot>

    <h2 class="at-heading is-3">
        {{ $article->name }}
    </h2>

    @if ($article->summary)
        <x-slot name="content">
            <p>{{ $article->summary }}</p>
        </x-slot>
    @endif
</x-numaxlab-atomic::molecules.summary>