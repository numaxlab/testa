<div class="container mx-auto px-4">
    <x-numaxlab-atomic::organisms.tier class="mb-10">
        <x-numaxlab-atomic::organisms.tier.header>
            <h2 class="at-heading is-2">
                {{ $tier->name }}
            </h2>

            @if ($tier->has_link)
                <a href="{{ $tier->link }}"
                   class="at-small"
                >
                    {{ $tier->link_name }}
                </a>
            @endif
        </x-numaxlab-atomic::organisms.tier.header>

        <ul class="grid grid-flow-col auto-cols-[60%] lg:auto-cols-[35%] gap-6">
            @foreach ($attachments as $attachment)
                <li>
                    <x-dynamic-component
                            :component="'trafikrak::'.$attachment->component_namespace.'.summary'"
                            :media="$attachment->media"/>
                </li>
            @endforeach
        </ul>
    </x-numaxlab-atomic::organisms.tier>
</div>