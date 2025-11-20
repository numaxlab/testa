<x-numaxlab-atomic::molecules.summary
        href="{{ route('trafikrak.storefront.education.courses.modules.show', [$module->course->defaultUrl->slug, $module->defaultUrl->slug]) }}"
>
    <x-slot name="thumbnail">
        <img src="{{ $module->course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">

        <span class="at-tag at-small absolute top-0 left-0 bg-primary border-primary text-white">{{ __('Sesión de curso') }}</span>
    </x-slot>

    <h2 class="at-heading is-3">
        {{ $module->name }}
    </h2>
    @if ($module->subtitle)
        <h3 class="at-heading is-4 text-black font-normal">
            {{ $module->subtitle }}
        </h3>
    @endif

    <x-slot name="content">
        <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
            <li class="pr-2">
                <i class="icon icon-calendar text-2xl mr-2" aria-hidden="true"></i>
                <time datetime="{{ $module->starts_at->format('Y-m-d H:i:s') }}">
                    {{ $module->starts_at->format('d/m/Y H:i') }}
                </time>
            </li>
            @if ($module->location)
                <li>
                    {{ $module->location }}
                </li>
            @endif
        </ul>
    </x-slot>
</x-numaxlab-atomic::molecules.summary>