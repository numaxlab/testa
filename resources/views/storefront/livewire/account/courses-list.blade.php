<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('dashboard') }}">
                    {{ __('Mi cuenta') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Mis cursos') }}
        </h1>
    </header>

    @if ($courses->isNotEmpty())
        <ul class="grid gap-6 md:grid-cols-2">
            @foreach ($courses as $course)
                <li>
                    <x-trafikrak::courses.summary :course="$course"/>
                </li>
            @endforeach
        </ul>

        {{ $courses->links() }}
    @else
        <p>{{ __('Todavía no te has inscrito en ningún curso.') }}</p>
    @endif
</article>