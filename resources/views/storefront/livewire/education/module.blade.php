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
            <li>
                <a href="{{ route('trafikrak.storefront.education.courses.show', $module->course->defaultUrl->slug) }}">
                    {{ $module->course->name }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">{{ $module->name }}</h1>

        @if ($module->subtitle)
            <h2 class="at-heading is-3 font-normal">{{ $module->subtitle }}</h2>
        @endif
    </header>

    <div class="mt-6 lg:flex lg:gap-6">
        <figure class="mb-6 lg:w-4/12">
            <img src="{{ $module->course->getFirstMediaUrl(config('lunar.media.collection'), 'large') }}" alt="">
        </figure>

        <div class="lg:w-8/12">
            <div class="lg:flex lg:flex-row-reverse lg:gap-6">
                <div class="lg:w-4/12">
                    <ul class="text-sm border-y border-black divide-x divide-black flex gap-2 py-2">
                        <li class="pr-2">
                            <i class="fa-solid fa-calendar text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                            <time datetime="{{ $module->starts_at->format('Y-m-d') }}">{{ $module->starts_at->format('d/m/Y H:i') }}</time>
                        </li>
                    </ul>
                    <div class="flex gap-2 border-b border-black py-2">
                        <i class="fa-solid fa-info text-2xl w-5 text-center mr-2" aria-hidden="true"></i>
                        <p class="at-small">
                            {{ __('trafikrak::coursemodule.form.delivery_method.options.'.$module->delivery_method->value) }}
                            @if ($module->location)
                                <br>
                                {{ $module->location }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="my-10 lg:w-8/12 lg:mt-0">
                    {!! $module->description !!}
                </div>
            </div>

            @if ($module->instructors->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Personas') }}
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

            <article class="bg-secondary pt-5 pb-50 px-5 mt-10">
                <h2>Báner 1</h2>
            </article>

            @if ($module->products->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Relacionados') }}
                        </h2>

                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 grid-cols-2 mb-9 md:grid-cols-4">
                        @foreach ($module->products as $product)
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

            @if ($module->attachments->isNotEmpty())
                <x-numaxlab-atomic::organisms.tier class="mt-10">
                    <x-numaxlab-atomic::organisms.tier.header>
                        <h2 class="at-heading is-2">
                            {{ __('Mediateca') }}
                        </h2>

                    </x-numaxlab-atomic::organisms.tier.header>

                    <ul class="grid gap-6 md:grid-cols-2">
                        @foreach ($module->attachments as $attachment)
                            <li>
                                @if ($attachment->media_type === \Trafikrak\Models\Media\Video::class)
                                    <x-trafikrak::videos.summary/>
                                @elseif($attachment->media_type === \Trafikrak\Models\Media\Audio::class)
                                    <x-trafikrak::audios.summary/>
                                @elseif($attachment->media_type === \Trafikrak\Models\Media\Document::class)
                                    <x-trafikrak::documents.summary>
                                        <x-slot name="thumbnail">
                                            <img src="https://picsum.photos/600/800" alt="" loading="lazy">
                                        </x-slot>

                                        <h2 class="at-heading is-3">
                                            Título del documento
                                        </h2>

                                        <x-slot name="content">
                                            <p>Descripción del recurso multimedia.</p>
                                        </x-slot>
                                    </x-trafikrak::documents.summary>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-numaxlab-atomic::organisms.tier>
            @endif

            <livewire:trafikrak.storefront.livewire.components.education.modules-list
                    lazy
                    :course="$module->course"
                    :except="$module"
                    :title="__('Sesiones')"
            />
        </div>
    </div>
</article>