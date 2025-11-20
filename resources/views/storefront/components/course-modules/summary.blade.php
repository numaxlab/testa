<x-numaxlab-atomic::molecules.summary
        href="{{ route('trafikrak.storefront.education.courses.modules.show', [$module->course->defaultUrl->slug, $module->defaultUrl->slug]) }}">
    <h2 class="at-heading is-3">
        {{ $module->name }}
    </h2>
    @if ($module->subtitle)
        <h3 class="at-heading is-4 text-black font-normal">
            {{ $module->subtitle }}
        </h3>
    @endif

    <x-slot name="content">
        @if ($module->description)
            <div class="at-small mb-4">
                {!! \Illuminate\Support\Str::limit($module->description, 150) !!}
            </div>
        @endif

        <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
            <li class="pr-2">
                <i class="icon icon-calendar text-2xl mr-2" aria-hidden="true"></i>
                <time datetime="{{ $module->starts_at->format('Y-m-d H:i:s') }}">
                    {{ $module->starts_at->format('d/m/Y H:i') }}
                </time>
            </li>
        </ul>
    </x-slot>
</x-numaxlab-atomic::molecules.summary>