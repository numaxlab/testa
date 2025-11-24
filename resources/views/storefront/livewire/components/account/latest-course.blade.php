<div>
    <x-numaxlab-atomic::organisms.tier>
        <x-numaxlab-atomic::organisms.tier.header class="flex gap-2">
            <h2 class="at-heading is-2">{{ __('Último curso') }}</h2>
            <a
                    class="at-small at-button is-secondary"
                    href="{{ route('my-courses.index') }}"
                    wire:navigate
            >
                {{ __('Ver todos') }}
            </a>
        </x-numaxlab-atomic::organisms.tier.header>

        @if ($course)
            <x-trafikrak::courses.summary :course="$course"/>
        @else
            <p>{{ __('Todavía no te has inscrito en ningún curso.') }}</p>
        @endif
    </x-numaxlab-atomic::organisms.tier>
</div>