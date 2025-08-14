<x-numaxlab-atomic::molecules.summary
        href="{{ route('trafikrak.storefront.education.courses.show', $course->defaultUrl->slug) }}">
    <x-slot name="thumbnail">
        <img src="{{ $course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">

        @if ($course->topic)
            <span class="at-tag at-small absolute top-0 left-0 bg-primary border-primary text-white">{{ $course->topic->name }}</span>
        @endif
    </x-slot>

    <h2 class="at-heading is-3">
        {{ $course->name }}
    </h2>
    @if ($course->subtitle)
        <h3 class="at-heading is-4 text-black font-normal">
            {{ $course->subtitle }}
        </h3>
    @endif

    <x-slot name="content">
        <div class="at-small mb-4">
            <p>Un breve texto explicativo del contenido de este curso.</p>
        </div>

        <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
            <li class="pr-2">
                <i class="fa-solid fa-calendar text-2xl mr-2" aria-hidden="true"></i>
                <time datetime="{{ $course->starts_at->format('Y-m-d') }}">{{ $course->starts_at->format('d/m/Y') }}</time>
                -
                <time datetime="{{ $course->ends_at->format('Y-m-d') }}">{{ $course->ends_at->format('d/m/Y') }}</time>
            </li>
            @if ($course->location)
                <li>
                    {{ $course->location }}
                </li>
            @endif
        </ul>
    </x-slot>
</x-numaxlab-atomic::molecules.summary>