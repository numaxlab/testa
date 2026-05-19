<div class="lg:flex lg:flex-row-reverse lg:gap-10">
    <div class="mt-10 lg:w-1/3 lg:mt-0 xl:w-1/4">
        @if ($pricing)
            <div class="at-heading is-3 mb-1 font-titles">
                {{ $pricing->priceIncTax()->formatted() }}
            </div>

            <livewire:testa.storefront.livewire.components.bookshop.product-availability
                    lazy
                    :key="'availability-' . $product->id"
                    :purchasable="$product->variant"/>
        @endif

        <livewire:testa.storefront.livewire.components.bookshop.add-to-cart
                :key="'add-to-cart-' . $product->id"
                :purchasable="$product->variant"/>

        @if ($product->translateAttribute('digital-book'))
            <a class="at-button border-primary text-primary">
                {{ __('Descarga este libro') }}
            </a>
        @endif

        @if ($product->brand?->translateAttribute('in-house') === true)
            <a class="at-button border-primary text-primary">
                {{ __('Haz una donación') }}
            </a>
        @endif

        @if ($product->translateAttribute('card'))
            <a class="at-button border-primary text-primary">
                {{ __('Descargar ficha') }}
            </a>
        @endif
    </div>

    <div class="mt-10 lg:mt-0 lg:w-2/3 xl:w-3/4">
        @if ($taxonomies->isNotEmpty())
            <ul class="flex flex-wrap gap-2">
                @foreach ($taxonomies as $taxonomy)
                    <li>
                        <a
                                @if ($taxonomy['href'])
                                    href="{{ $taxonomy['href'] }}"
                                @endif
                                wire:navigate
                                class="at-small at-tag"
                        >
                            {{ $taxonomy['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <ul class="font-bold at-small flex flex-wrap gap-1 mt-2 mb-3 [&>li:not(:last-child)]:after:content-['|']">
            @if ($product->brand)
                <li>
                    {{ $product->brand->name }}
                </li>
            @endif
            @foreach ($editorialCollections as $collection)
                <li>
                    <a
                            href="{{ route('testa.storefront.editorial.collections.show', $collection->defaultUrl->slug) }}"
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

        @if ($synopsis)
            <div x-data="lineClamp">
                <div x-ref="description" :class="{ 'line-clamp-9': !expanded }" id="product-synopsis">
                    {!! $synopsis !!}
                </div>

                <button
                        x-show="clamped"
                        :aria-expanded="expanded"
                        aria-controls="product-synopsis"
                        @click.prevent="expanded = !expanded"
                        class="text-primary"
                >
                    <span x-text="expanded ? '{{ __('Leer menos') }}' : '{{ __('Leer más') }}'"></span>
                </button>
            </div>
        @endif

        <ul class="at-small text-grey flex flex-wrap gap-1 mt-3 mb-1 [&>li:not(:last-child)]:after:content-['|']">
            @if ($product->translators->isNotEmpty())
                <li>
                    {{ __('Traducción') }}:
                    @foreach ($product->translators as $author)
                        {{ $author->name }}{{ $loop->last ? '' : ', ' }}
                    @endforeach
                </li>
            @endif
            @if ($product->illustrators->isNotEmpty())
                <li>
                    {{ __('Ilustración') }}:
                    @foreach ($product->illustrators as $author)
                        {{ $author->name }}{{ $loop->last ? '' : '; ' }}
                    @endforeach
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
            @foreach ($product->bindingTypes as $bindingType)
                <li>
                    {{ $bindingType->translateAttribute('name') }}
                </li>
            @endforeach
            @if ($product->variant->width->getValue() && $product->variant->height->getValue())
                <li>
                    {{ $product->variant->width->to('length.mm')->format() }}
                    x
                    {{ $product->variant->height->to('length.mm')->format() }}
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
