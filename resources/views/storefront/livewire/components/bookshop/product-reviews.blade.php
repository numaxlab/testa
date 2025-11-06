<div>
    @if ($reviews->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Sobre este libro se ha dicho...') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="flex flex-col gap-4 md:flex-row md:gap-6">
                @foreach ($reviews as $review)
                    <li class="pr-10 md:max-w-1/2">
                        <x-trafikrak::reviews.summary :review="$review"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</div>