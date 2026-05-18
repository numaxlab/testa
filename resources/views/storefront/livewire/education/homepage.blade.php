<x-slot name="description">{{ Str::limit(strip_tags($slides->first()?->description), 160) }}</x-slot>

<div>
    <x-testa::slides.carousel :slides="$slides"/>

    @foreach ($tiers as $tier)
        <livewire:dynamic-component
                :component="'testa.storefront.livewire.components.tier.' . $tier->livewire_component"
                :lazy="!$loop->first"
                :tier="$tier"
                :key="$tier->id"
        />
    @endforeach
</div>