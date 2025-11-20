<article class="container mx-auto px-4">
    <div class="mt-6 lg:flex lg:gap-6">
        <figure class="mb-6 lg:w-4/12">
            <img src="{{ $course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">
        </figure>

        <div class="lg:w-8/12">
            <header class="mb-6">
                <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
                    <li>
                        <a href="{{ route('trafikrak.storefront.education.homepage') }}" wire:navigate>
                            {{ __('Formación') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trafikrak.storefront.education.courses.index') }}" wire:navigate>
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
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1 mt-4">{{ $course->name }}</h1>

                @if ($course->subtitle)
                    <h2 class="at-heading is-3 font-normal mt-2">{{ $course->subtitle }}</h2>
                @endif
            </header>

            <div class="lg:flex lg:flex-row-reverse lg:gap-6">
                <div class="lg:w-4/12">
                    <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
                        <li class="pr-2">
                            <i class="icon icon-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $course->starts_at->format('Y-m-d') }}">{{ $course->starts_at->format('d/m/Y') }}</time>
                            -
                            <time datetime="{{ $course->ends_at->format('Y-m-d') }}">{{ $course->ends_at->format('d/m/Y') }}</time>
                        </li>
                    </ul>
                    <div class="flex gap-2 border-b border-black py-2">
                        <i class="icon icon-info text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                        <p class="at-small">
                            Aut adis providi caborepudam, eat ex ea sim in pos dolor aut doluptatem
                            quiditatis magnur, est et ullupta tiaspientia nonsequis aute velias.
                        </p>
                    </div>
                    @if ($course->purchasable)
                        <a
                                href="{{ route('trafikrak.storefront.education.courses.register', $course->defaultUrl->slug) }}"
                                wire:navigate class="at-button is-primary mt-4"
                        >
                            {{ __('Inscríbete') }}
                        </a>
                    @endif
                </div>
                @if ($course->description)
                    <div x-data="lineClamp" class="my-10 lg:w-8/12 lg:mt-0">
                        <div x-ref="description" :class="{ 'line-clamp-14': !showMore }">
                            {!! $course->description !!}
                        </div>

                        <button x-show="!showMore" @click.prevent="showMore = true" class="text-primary">
                            {{ __('Leer más') }}
                        </button>
                    </div>
                @endif
            </div>

            <livewire:trafikrak.storefront.livewire.components.education.course-media
                    lazy
                    :course="$course"
            />

            <livewire:trafikrak.storefront.livewire.components.education.course-modules
                    lazy
                    :course="$course"
                    :title="__('Sesiones')"
            />

            <livewire:trafikrak.storefront.livewire.components.education.course-products
                    lazy
                    :course="$course"
            />

            @if ($banner)
                <div class="mt-10">
                    <x-trafikrak::banners.mini :banner="$banner"/>
                </div>
            @endif
        </div>
    </div>
</article>