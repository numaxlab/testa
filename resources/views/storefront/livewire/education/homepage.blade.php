<x-slot name="description">{{ Str::limit(strip_tags($slides->first()?->description), 160) }}</x-slot>

<div>
    @if ($slides->isNotEmpty())
        <div class="-mt-10">
            @foreach ($slides as $slide)
                <x-testa::slides.full-width :slide="$slide"/>
            @endforeach
        </div>
    @endif

    @foreach ($tiers as $tier)
        <livewire:dynamic-component
                :component="'testa.storefront.livewire.components.tier.' . $tier->livewire_component"
                :lazy="!$loop->first"
                :tier="$tier"
                :key="$tier->id"
        />
    @endforeach
</div>