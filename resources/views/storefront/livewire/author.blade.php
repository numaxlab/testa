<x-slot name="description">{{ Str::limit(strip_tags($author->translateAttribute('biography')), 160) }}</x-slot>
<x-slot name="ogImage">{{ $author->getFirstMediaUrl(config('lunar.media.collection'), 'open-graph') }}</x-slot>

<x-slot name="head">
    <meta property="og:type" content="profile">
    <meta property="profile:first_name" content="{{ $author->first_name }}">
    <meta property="profile:last_name" content="{{ $author->last_name }}">
</x-slot>

<article class="{{ $hasMedia ? '-mb-20' : '' }}">
    <div class="container mx-auto px-4">
        <header class="md:flex gap-6">
            <div class="md:w-1/2 lg:pr-20">
                <h1 class="at-heading is-1">{{ $author->name }}</h1>

                @if ($author->translateAttribute('biography'))
                    <div class="mt-5">
                        {!! $author->translateAttribute('biography') !!}
                    </div>
                @endif
            </div>

            @if ($author->hasMedia(config('lunar.media.collection')))
                <figure class="mt-5 md:w-1/2">
                    <img src="{{ $author->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}"
                         alt="">
                </figure>
            @endif
        </header>

        @if ($products->isNotEmpty())
            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Libros') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($products as $product)
                        <li>
                            <x-testa::products.summary
                                    :product="$product"
                                    :href="route('testa.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                            />
                        </li>
                    @endforeach
                </ul>

                {{ $products->links() }}
            </x-numaxlab-atomic::organisms.tier>
        @endif
    </div>

    @if ($hasMedia)
        <div class="bg-secondary">
            <div class="container mx-auto px-4 py-6">
                <livewire:testa.storefront.livewire.components.author.media
                        lazy
                        :author="$author"
                />

                <livewire:testa.storefront.livewire.components.author.events
                        lazy
                        :author="$author"
                />
            </div>
        </div>
    @endif
</article>