<div>
    @if ($this->manualAssociations->isNotEmpty() || $this->automaticAssociations->isNotEmpty())
        <x-testa::tier.horizontal-scroll>
            <x-slot name="title">
                {{ __('Relacionados') }}
            </x-slot>

            <ul class="grid grid-flow-col auto-cols-[40%] md:auto-cols-[25%] gap-6">
                @foreach ($this->manualAssociations as $association)
                    <li>
                        <x-testa::products.summary
                                :product="$association->target"
                                :href="route('testa.storefront.bookshop.products.show', $association->target->defaultUrl->slug)"
                        />
                    </li>
                @endforeach

                @foreach ($this->automaticAssociations as $product)
                    <li>
                        <x-testa::products.summary
                                :product="$product"
                                :href="route('testa.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>
        </x-testa::tier.horizontal-scroll>
    @endif
</div>