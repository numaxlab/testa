<x-numaxlab-atomic::molecules.summary
        href="{{ route('trafikrak.storefront.events.show', $event->defaultUrl->slug) }}">
    <x-slot name="thumbnail">
        <img src="{{ Storage::url($event->image) }}" alt="">

        @if ($event->eventType)
            <span class="at-tag at-small absolute top-0 left-0 bg-primary border-primary text-white">{{ $event->eventType->name }}</span>
        @endif
    </x-slot>

    <h2 class="at-heading is-3">
        {{ $event->name }}
    </h2>
    @if ($event->subtitle)
        <h3 class="at-heading is-4 text-black font-normal">
            {{ $event->subtitle }}
        </h3>
    @endif

    <x-slot name="content">
        <ul class="font-sans text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
            <li class="pr-2">
                <i class="icon icon-calendar text-2xl mr-2" aria-hidden="true"></i>
                <time datetime="{{ $event->starts_at->format('Y-m-d H:i:s') }}">
                    {{ $event->starts_at->format('d/m/Y H:i') }}
                </time>
            </li>
            @if ($event->location)
                <li>
                    {{ $event->location }}
                </li>
            @endif
        </ul>
    </x-slot>
</x-numaxlab-atomic::molecules.summary>