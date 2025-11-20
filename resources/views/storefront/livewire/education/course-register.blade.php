<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-8">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('trafikrak.storefront.education.homepage') }}">
                    {{ __('Formación') }}
                </a>
            </li>
            <li>
                <a href="{{ route('trafikrak.storefront.education.courses.index') }}">
                    {{ __('Cursos') }}
                </a>
            </li>
            @if ($course->topic)
                <li>
                    <a
                            href="{{ route('trafikrak.storefront.education.topics.show', $course->topic->defaultUrl->slug) }}"
                            wire:navigate
                    >
                        {{ $course->topic->name }}
                    </a>
                </li>
            @endif
            <li>
                <a
                        href="{{ route('trafikrak.storefront.education.courses.show', $course->defaultUrl->slug) }}"
                        wire:navigate
                >
                    {{ $course->name }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1 mt-2">{{ __('Inscripción') }}</h1>
    </header>

    <article class="mb-15">
        <h3 class="at-heading is-2">
            {{ $course->name }}
        </h3>
        @if ($course->subtitle)
            <p class="text-2xl mt-2">
                {{ $course->subtitle }}
            </p>
        @endif
        <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2 mt-4">
            <li class="pr-2">
                <i class="icon icon-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                <time datetime="{{ $course->starts_at->format('Y-m-d') }}">{{ $course->starts_at->format('d/m/Y') }}</time>
                -
                <time datetime="{{ $course->ends_at->format('Y-m-d') }}">{{ $course->ends_at->format('d/m/Y') }}</time>
            </li>
        </ul>
    </article>

    @if ($banner)
        <div class="mb-10">
            <x-trafikrak::banners.mini :banner="$banner"/>
        </div>
    @endif

    <form>
        <fieldset class="mb-8">
            <legend class="at-heading is-2 float-left w-full">
                {{ __('Elige el tipo de inscripción') }}
            </legend>

            <ul class="grid gap-4 md:grid-cols-2 mt-15 mb-5 clear-both">
                @foreach ($course->purchasable->variants as $variant)
                    <li wire:key="variant-{{ $variant->id }}">
                        <input type="radio"
                               name="variant"
                               id="variant{{ $variant->id }}"
                               wire:model.live="selectedVariant"
                               value="{{ $variant->id }}"
                               class="sr-only peer">

                        <label for="variant{{ $variant->id }}"
                               class="block cursor-pointer border rounded-lg h-full p-6 transition duration-150 relative
                                    border-black bg-transparent text-gray-800 prose hover:border-[var(--color-primary)]
                                    peer-checked:border-[var(--color-primary)] peer-checked:bg-[var(--color-primary)] peer-checked:text-white!
                                    peer-checked:prose-invert"
                        >
                            <div class="text-2xl font-semibold not-prose">
                                {{ $variant->values->pluck('name.'.app()->getLocale())->join(' - ') }}:

                                <livewire:trafikrak.storefront.livewire.components.price
                                        :key="'price-' . $variant->id"
                                        :purchasable="$variant"/>
                            </div>
                        </label>
                    </li>
                @endforeach
            </ul>

            <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('selectedVariant')"/>
        </fieldset>

        <fieldset class="mb-8">
            <legend class="at-heading is-2 float-left w-full">
                {{ __('Método de pago') }}
            </legend>

            <div class="mt-15 clear-both">
                @include('trafikrak::storefront.partials.signup.payment')
            </div>
        </fieldset>

        <div class="mb-8">
            @include('trafikrak::storefront.partials.privacy-policy')
        </div>

        <button class="at-button is-primary w-full" type="submit">
            {{ __('Inscribirme') }}
        </button>
    </form>
</article>