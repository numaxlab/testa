<x-slot name="description">{{ Str::limit(strip_tags($course->description), 160) }}</x-slot>
<x-slot name="ogImage">{{ $course->getFirstMediaUrl(config('lunar.media.collection'), 'open-graph') }}</x-slot>

<x-slot name="bodyClass">bg-secondary</x-slot>

<article class="container mx-auto px-4 lg:max-w-4xl">
    <header class="mb-8">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('testa.storefront.education.homepage') }}">
                    {{ __('Formación') }}
                </a>
            </li>
            <li>
                <a href="{{ route('testa.storefront.education.courses.index') }}">
                    {{ __('Cursos') }}
                </a>
            </li>
            @if ($course->topic)
                <li>
                    <a
                            href="{{ route('testa.storefront.education.topics.show', $course->topic->defaultUrl->slug) }}"
                            wire:navigate
                    >
                        {{ $course->topic->name }}
                    </a>
                </li>
            @endif
            <li>
                <a
                        href="{{ route('testa.storefront.education.courses.show', $course->defaultUrl->slug) }}"
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
            <x-testa::banners.mini :banner="$banner"/>
        </div>
    @endif

    <form wire:submit="register">
        <fieldset class="mb-10">
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

                                <livewire:testa.storefront.livewire.components.price
                                        :key="'price-' . $variant->id"
                                        :purchasable="$variant"/>
                            </div>

                            @if ($variant->translateAttribute('description'))
                                <div class="mt-2 text-base opacity-90">
                                    {!! $variant->translateAttribute('description') !!}
                                </div>
                            @endif
                        </label>
                    </li>
                @endforeach
            </ul>

            <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('selectedVariant')"/>
        </fieldset>

        @if (!Auth::check())
            <fieldset class="mb-10">
                <legend class="at-heading is-2 float-left w-full">
                    {{ __('Tus datos') }}
                </legend>

                <div class="mt-15 clear-both">
                    @include('testa::storefront.partials.checkout.embed-auth')
                </div>
            </fieldset>
        @endif

        <fieldset class="mb-10">
            <legend class="at-heading is-2 float-left w-full">
                {{ __('Necesitas factura?') }}
            </legend>

            <div class="mt-15 clear-both">
                <ul class="flex gap-6">
                    <li>
                        <x-numaxlab-atomic::atoms.forms.radio
                                wire:model.live="invoice"
                                id="invoice-no"
                                key="invoiceNo"
                                name="invoice"
                                value="0">
                        <span class="text-2xl">
                            {{ __('No') }}
                        </span>
                        </x-numaxlab-atomic::atoms.forms.radio>
                    </li>
                    <li>
                        <x-numaxlab-atomic::atoms.forms.radio
                                wire:model.live="invoice"
                                id="invoice-yes"
                                key="invoiceYes"
                                name="invoice"
                                value="1">
                        <span class="text-2xl">
                            {{ __('Si') }}
                        </span>
                        </x-numaxlab-atomic::atoms.forms.radio>
                    </li>
                </ul>

                @if ($this->invoice)
                    <div class="mt-6">
                        @include('testa::storefront.partials.checkout.billing-address')
                    </div>
                @endif
            </div>
        </fieldset>

        <fieldset class="mb-10">
            <legend class="at-heading is-2 float-left w-full">
                {{ __('Método de pago') }}
            </legend>

            <div class="mt-15 clear-both">
                @include('testa::storefront.partials.checkout.payment')
            </div>
        </fieldset>

        <div class="mb-8">
            @include('testa::storefront.partials.privacy-policy')
        </div>

        <x-testa::loading-button target="register" class="is-primary w-full">
            {{ __('Inscribirme') }}
        </x-testa::loading-button>
    </form>
</article>