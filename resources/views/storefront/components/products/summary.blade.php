<article>
    <a href="{{ $attributes->get('href') }}" wire:navigate class="block mb-1 hover:no-underline group">
        <img
                src="{{ $product->getFirstMediaUrl(config('lunar.media.collection'), 'medium') }}"
                loading="lazy"
                alt=""/>

        <h3 class="at-heading is-4 mt-3 text-black group-hover:text-black/70">
            {{ $product->recordTitle }}
        </h3>
    </a>

    @if ($product->authors->isNotEmpty())
        <div class="mb-2">
            <ul>
                @foreach ($product->authors as $author)
                    <li>
                        <p class="at-small leading-4 mb-1">{{ $author->name }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <livewire:trafikrak.storefront.livewire.components.bookshop.add-to-cart
            :key="'add-to-cart-' . $product->id"
            :purchasable="$product->variant"
            :display-price="true"/>
</article>
