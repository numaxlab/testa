<div class="lg:flex lg:gap-6">
    <div class="lg:w-2/3">
        @if ($product->taxonomies->isNotEmpty())
            <ul class="flex flex-wrap gap-2">
                @foreach ($product->taxonomies as $taxonomy)
                    @if ($taxonomy->isInSectionTree() && ! $taxonomy->isRoot())
                        <li>
                            <a
                                    @if ($taxonomy->getAncestorSection() && $taxonomy->getAncestorSection()->defaultUrl)
                                        href="{{ route('trafikrak.storefront.bookshop.sections.show', ['slug' => $taxonomy->getAncestorSection()->defaultUrl->slug, 't' => $taxonomy->id]) }}"
                                    @endif
                                    wire:navigate
                                    class="at-small at-tag is-primary"
                            >
                                {{ $taxonomy->translateAttribute('name') }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        <ul class="at-small flex flex-wrap gap-2 -ml-2 my-3">
            @if ($product->brand)
                <li>
                    {{ $product->brand->name }}
                </li>
            @endif
            @foreach ($product->editorialCollections as $collection)
                <li>
                    <a href="" wire:navigate>
                        {{ $collection->translateAttribute('name') }}
                    </a>
                </li>
            @endforeach
            @if ($product->translateAttribute('first-issue-year'))
                <li>
                    {{ $product->translateAttribute('first-issue-year') }}
                </li>
            @endif
            @if ($product->translateAttribute('pages'))
                <li>
                    {{ $product->translateAttribute('pages') }} {{ __('páginas') }}
                </li>
            @endif
            @foreach ($product->languages as $language)
                <li>
                    {{ $language->translateAttribute('name') }}
                </li>
            @endforeach
        </ul>

        @if ($product->translateAttribute('bookshop-reference'))
            <div>
                {!! $product->translateAttribute('bookshop-reference') !!}
            </div>
        @elseif ($product->translateAttribute('editorial-reference'))
            <div>
                {!! $product->translateAttribute('editorial-reference') !!}
            </div>
        @endif

        <ul class="at-small mt-5">
            @if ($product->variant->width->getValue() && $product->variant->height->getValue())
                <li>
                    {{ $product->variant->width->to('length.cm')->format() }}
                    x
                    {{ $product->variant->height->to('length.cm')->format() }}
                </li>
            @endif
            @if ($product->variant->weight->getValue())
                <li>
                    {{ $product->variant->width->to('weight.g')->format() }}
                </li>
            @endif
            @if ($product->variant->gtin)
                <li>
                    ISBN: {{ $product->variant->gtin }}
                </li>
            @endif
            @if ($product->variant->ean)
                <li>
                    EAN: {{ $product->variant->ean }}
                </li>
            @endif
        </ul>
    </div>

    <div class="mt-10 lg:w-1/3 lg:mt-0">
        @if ($pricing)
            <div class="at-heading is-3 mb-3">
                {{ $pricing->priceIncTax()->formatted() }}
            </div>
        @endif

        @foreach ($product->statuses as $status)
            <span class="text-primary mb-3">
                {{ $status->translateAttribute('name') }}
            </span>
        @endforeach

        <livewire:trafikrak.storefront.livewire.components.bookshop.add-to-cart
                :key="'add-to-cart-' . $product->id"
                :purchasable="$product->variant"/>

        @if ($product->translateAttribute('digital-book'))
            <a class="at-button border-primary text-primary">
                Descarga este libro
            </a>
        @endif

        @if ($product->brand->translateAttribute('in-house') === true)
            <a class="at-button border-primary text-primary">
                Haz una donación
            </a>
        @endif

        @if ($product->translateAttribute('card'))
            <a class="at-button border-primary text-primary">
                Descargar ficha
            </a>
        @endif
    </div>
</div>