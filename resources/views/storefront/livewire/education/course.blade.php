<article class="container mx-auto px-4">
    <header class="lg:w-8/12">
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
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">{{ $course->name }}</h1>

        @if ($course->subtitle)
            <h2 class="at-heading is-3 font-normal">{{ $course->subtitle }}</h2>
        @endif
    </header>

    <div class="mt-6 lg:flex lg:gap-6">
        <figure class="mb-6 lg:w-4/12">
            <img src="{{ $course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">
        </figure>

        <div class="lg:w-8/12">
            <div class="lg:flex lg:flex-row-reverse lg:gap-6">
                <div class="lg:w-4/12">
                    <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
                        <li class="pr-2">
                            <i class="fa-solid fa-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $course->starts_at->format('Y-m-d') }}">{{ $course->starts_at->format('d/m/Y') }}</time>
                            -
                            <time datetime="{{ $course->ends_at->format('Y-m-d') }}">{{ $course->ends_at->format('d/m/Y') }}</time>
                        </li>
                    </ul>
                    <div class="flex gap-2 border-b border-black py-2">
                        <i class="fa-solid fa-info text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                        <p class="at-small">
                            Tarifas: 100€ / 50€ (socias)<br>
                            Aviso para la inscripción: Aut adis providi caborepudam, eat ex ea sim in pos dolor aut
                            doluptatem
                            quiditatis magnur, est et ullupta tiaspientia nonsequis aute velias.
                        </p>
                    </div>
                </div>
                <div class="my-10 lg:w-8/12 lg:mt-0">
                    {!! $course->description !!}
                </div>
            </div>

            <article class="bg-secondary pt-5 pb-50 px-5 mb-10">
                <h2>Báner 1</h2>
            </article>

            <livewire:trafikrak.storefront.livewire.components.education.modules-list
                    lazy
                    :course="$course"
                    :title="__('Sesiones')"
            />

            @if ($course->products->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Relacionados') }}
                        </h2>

                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4">
                        @foreach ($course->products as $product)
                            <li>
                                <x-trafikrak::products.summary
                                        :product="$product"
                                        :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                                />
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif
        </div>
    </div>
</article>