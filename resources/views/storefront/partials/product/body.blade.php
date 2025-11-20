<div class="lg:flex lg:flex-row-reverse lg:gap-6">
    <div class="mt-10 lg:w-1/3 lg:mt-0">
        @if ($pricing)
            <div class="at-heading is-3 mb-1 font-titles">
                {{ $pricing->priceIncTax()->formatted() }}
            </div>
            <div class="font-serif font-small text-primary mb-3">
                Disponible <i class="icon icon-info" aria-hidden="true"></i>
            </div>
        @endif

        @foreach ($product->statuses as $status)
            <span class="text-primary mb-3">
                {{ $status->translateAttribute('name') }}
            </span>
        @endforeach

        <livewire:trafikrak.storefront.livewire.components.bookshop.add-to-cart
                :key="$prefix . 'add-to-cart-' . $product->id"
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

    <div class="mt-10 lg:mt-0 lg:w-2/3">
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
                                    class="at-small at-tag"
                            >
                                {{ $taxonomy->translateAttribute('name') }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        <ul class="font-bold at-small flex flex-wrap gap-1 mt-2 mb-3 [&>li:not(:last-child)]:after:content-['|']">
            @if ($product->brand)
                <li>
                    {{ $product->brand->name }}
                </li>
            @endif
            @foreach ($product->editorialCollections as $collection)
                <li>
                    <a
                            href="{{ route('trafikrak.storefront.editorial.collections.show', $collection->defaultUrl->slug) }}"
                            wire:navigate
                    >
                        {{ $collection->translateAttribute('name') }}
                    </a>
                </li>
            @endforeach
            @if ($product->translateAttribute('first-issue-year'))
                <li>
                    {{ $product->translateAttribute('first-issue-year') }}
                </li>
            @endif
        </ul>

        @if ($product->translateAttribute('bookshop-reference') || $product->translateAttribute('editorial-reference'))
            <div x-data="lineClamp">
                <div x-ref="description" :class="{ 'line-clamp-9 ': !showMore }">
                    @if ($product->translateAttribute('bookshop-reference'))
                        {!! $product->translateAttribute('bookshop-reference') !!}
                    @elseif ($product->translateAttribute('editorial-reference'))
                        {!! $product->translateAttribute('editorial-reference') !!}
                    @endif
                </div>

                <button x-show="!showMore" @click.prevent="showMore = true" class="text-primary">
                    {{ __('Leer más') }}
                </button>
            </div>
        @endif

        <ul class="at-small flex flex-wrap gap-1 mt-3 mb-1 [&>li:not(:last-child)]:after:content-['|']">
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
            @foreach ($product->bindingTypes as $bindingType)
                <li>
                    {{ $bindingType->translateAttribute('name') }}
                </li>
            @endforeach
            @if ($product->variant->width->getValue() && $product->variant->height->getValue())
                <li>
                    {{ $product->variant->width->to('length.cm')->format() }}
                    x
                    {{ $product->variant->height->to('length.cm')->format() }}
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
</div>