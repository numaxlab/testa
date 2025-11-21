<article class="container mx-auto px-4">
    <div class="mt-6 lg:flex lg:gap-6">
        <figure class="mb-6 lg:w-4/12">
            <img src="{{ $module->course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">
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
                    @if ($module->course->topic)
                        <li>
                            <a
                                    href="{{ route('trafikrak.storefront.education.topics.show', $module->course->topic->defaultUrl->slug) }}"
                                    wire:navigate
                            >
                                {{ $module->course->topic->name }}
                            </a>
                        </li>
                    @endif
                </x-numaxlab-atomic::molecules.breadcrumb>

                <h1 class="at-heading is-1 mt-4">{{ $module->name }}</h1>

                @if ($module->subtitle)
                    <h2 class="at-heading is-3 font-normal mt-2">{{ $module->subtitle }}</h2>
                @endif

                <div class="mt-6">
                    <a
                            href="{{ route('trafikrak.storefront.education.courses.show', $module->course->defaultUrl->slug) }}"
                            wire:navigate
                            class="at-tag is-primary"
                    >
                        {{ $module->course->name }}
                    </a>
                </div>
            </header>

            <div class="lg:flex lg:flex-row-reverse lg:gap-6">
                <div class="lg:w-4/12">
                    <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
                        <li class="pr-2">
                            <i class="icon icon-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $module->starts_at->format('Y-m-d H:i:s') }}">{{ $module->starts_at->format('d/m/Y H:i') }}</time>
                        </li>
                    </ul>
                    <div class="flex gap-2 border-b border-black py-2">
                        <i class="icon icon-info text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                        <p class="at-small">
                            {{ __('trafikrak::coursemodule.form.delivery_method.options.'.$module->delivery_method->value) }}
                            @if ($module->venue)
                                <br>
                                {{ $module->venue->name }}
                            @endif

                            @if ($module->alert)
                                <span class="block mt-2">{{ $module->alert }}</span>
                            @endif
                        </p>
                    </div>

                    @if ($module->course->purchasable)
                        <a
                                href="{{ route('trafikrak.storefront.education.courses.register', $module->course->defaultUrl->slug) }}"
                                wire:navigate class="at-button is-primary mt-4"
                        >
                            {{ __('Inscríbete') }}
                        </a>
                    @endif
                </div>
                @if ($module->description)
                    <div x-data="lineClamp" class="my-10 lg:w-8/12 lg:mt-0">
                        <div x-ref="description" :class="{ 'line-clamp-14': !showMore }">
                            {!! $module->description !!}
                        </div>

                        <button x-show="!showMore" @click.prevent="showMore = true" class="text-primary">
                            {{ __('Leer más') }}
                        </button>
                    </div>
                @endif
            </div>

            @if ($module->instructors->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="sr-only">
                            {{ __('Ponentes') }}
                        </h2>
                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 mb-10 md:grid-cols-2">
                        @foreach ($module->instructors as $instructor)
                            <li>
                                <x-trafikrak::authors.summary :author="$instructor"/>
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif

            <livewire:trafikrak.storefront.livewire.components.education.module-media
                    lazy
                    :module="$module"
            />

            <livewire:trafikrak.storefront.livewire.components.education.module-products
                    lazy
                    :module="$module"
            />

            <livewire:trafikrak.storefront.livewire.components.education.course-modules
                    lazy
                    :course="$module->course"
                    :except="$module"
                    :title="__('Más sesiones de este curso')"
            />
        </div>
    </div>
</article>