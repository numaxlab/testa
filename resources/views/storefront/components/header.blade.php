<div
        class="relative"
        x-data="{ menuExpanded: false, searchExpanded: false }"
>
    <div class="container mx-auto px-4">
        <header class="org-site-header lg:gap-10">
            <a class="text-xl font-bold" href="{{ route('trafikrak.storefront.homepage') }}" wire:navigate>
                {{ config('app.name') }}
            </a>

            <div class="lg:hidden">
                <x-trafikrak::header.actions/>
            </div>

            <nav
                    id="site-header-nav"
                    class="site-header-nav lg:flex lg:flex-col-reverse lg:grow"
                    :class="{ 'block': menuExpanded }"
            >
                <div class="lg:flex lg:w-full lg:justify-between">
                    <ul class="site-header-main-menu">
                        <li x-data="{ expanded: false }" class="relative">
                            <button @click="expanded = !expanded" class="text-primary">
                                {{ __('Librería') }}
                            </button>

                            <div x-cloak x-show="expanded"
                                 class="absolute top-13 z-10 flex gap-5 bg-white border-1 border-primary p-5 -ml-5">
                                <ul class="w-50">
                                    <li>
                                        <a
                                                href="{{ route('trafikrak.storefront.bookshop.homepage') }}"
                                                wire:navigate
                                        >
                                            {{ __('Librería') }}
                                        </a>
                                    </li>
                                    @if ($pages->has(\Trafikrak\Models\Content\Section::BOOKSHOP->value))
                                        @foreach ($pages->get(\Trafikrak\Models\Content\Section::BOOKSHOP->value) as $page)
                                            <li>
                                                <a
                                                        href="{{ route('trafikrak.storefront.bookshop.page', $page->defaultUrl->slug) }}"
                                                        wire:navigate
                                                >
                                                    {{ $page->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                                @if ($sections->isNotEmpty())
                                    <div class="w-122">
                                        <h3>{{ __('Secciones') }}</h3>

                                        <ul class="grid grid-cols-2">
                                            @foreach($sections as $collection)
                                                <li>
                                                    <a
                                                            href="{{ route('trafikrak.storefront.bookshop.sections.show', $collection->defaultUrl->slug) }}"
                                                            wire:navigate
                                                    >
                                                        {{ $collection->translateAttribute('name') }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li x-data="{ expanded: false }" class="relative">
                            <button @click="expanded = !expanded" class="text-primary">
                                {{ __('Editorial') }}
                            </button>

                            <div x-cloak x-show="expanded"
                                 class="absolute top-13 z-10 flex gap-5 bg-white border-1 border-primary p-5 -ml-5">
                                <ul class="w-50">
                                    <li>
                                        <a
                                                href="{{ route('trafikrak.storefront.editorial.homepage') }}"
                                                wire:navigate
                                        >
                                            {{ __('Editorial') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                                href="{{ route('trafikrak.storefront.editorial.authors.index') }}"
                                                wire:navigate
                                        >
                                            {{ __('Autoras') }}
                                        </a>
                                    </li>
                                    @if ($editorialSpecialCollections->isNotEmpty())
                                        @foreach($editorialSpecialCollections as $collection)
                                            <li>
                                                <a
                                                        href=""
                                                        wire:navigate
                                                >
                                                    {{ $collection->translateAttribute('name') }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                    @if ($pages->has(\Trafikrak\Models\Content\Section::EDITORIAL->value))
                                        @foreach ($pages->get(\Trafikrak\Models\Content\Section::EDITORIAL->value) as $page)
                                            <li>
                                                <a
                                                        href="{{ route('trafikrak.storefront.editorial.page', $page->defaultUrl->slug) }}"
                                                        wire:navigate
                                                >
                                                    {{ $page->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                                @if ($editorialCollections->isNotEmpty())
                                    <div class="w-122">
                                        <h3>{{ __('Colecciones') }}</h3>

                                        <ul class="grid grid-cols-2">
                                            @foreach($editorialCollections as $collection)
                                                <li>
                                                    <a
                                                            href="{{ route('trafikrak.storefront.editorial.collections.show', $collection->defaultUrl->slug) }}"
                                                            wire:navigate
                                                    >
                                                        {{ $collection->translateAttribute('name') }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </li>

                        <li x-data="{ expanded: false }" class="relative">
                            <button @click="expanded = !expanded" class="text-primary">
                                {{ __('Formación') }}
                            </button>

                            <ul x-cloak x-show="expanded"
                                class="absolute top-13 z-10 bg-white border-1 border-primary p-5 -ml-5">
                                <li>
                                    <a href="{{ route('trafikrak.storefront.education.homepage') }}" wire:navigate>
                                        {{ __('Formación') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('trafikrak.storefront.education.topics.index') }}" wire:navigate>
                                        {{ __('Temas') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('trafikrak.storefront.education.courses.index') }}" wire:navigate>
                                        {{ __('Cursos') }}
                                    </a>
                                </li>
                                @if ($pages->has(\Trafikrak\Models\Content\Section::EDUCATION->value))
                                    @foreach ($pages->get(\Trafikrak\Models\Content\Section::EDUCATION->value) as $page)
                                        <li>
                                            <a
                                                    href="{{ route('trafikrak.storefront.education.page', $page->defaultUrl->slug) }}"
                                                    wire:navigate
                                            >
                                                {{ $page->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                                <li>
                                    <a>
                                        {{ __('Subscríbete') }}
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        {{ __('Mis cursos') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li x-data="{ expanded: false }" class="relative">
                            <button @click="expanded = !expanded" class="text-primary">
                                {{ __('Mediateca') }}
                            </button>

                            <ul x-cloak x-show="expanded"
                                class="absolute top-13 z-10 bg-white border-1 border-primary p-5 -ml-5">
                                <li>
                                    <a href="{{ route('trafikrak.storefront.media.homepage') }}" wire:navigate>
                                        {{ __('Mediateca') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('trafikrak.storefront.media.videos.index') }}">
                                        {{ __('Vídeos') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('trafikrak.storefront.media.audios.index') }}">
                                        {{ __('Audios') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('trafikrak.storefront.media.documents.index') }}">
                                        {{ __('Documentos') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                {{ __('Actividades') }}
                            </a>
                        </li>
                    </ul>

                    <div class="hidden lg:block lg:relative">
                        <x-trafikrak::header.actions/>
                    </div>
                </div>

                <ul class="mb-5">
                    <li><a>Menú de utilidades</a></li>
                </ul>
            </nav>
        </header>
    </div>

    <div
            class="absolute inset-0 hidden"
            :class="{ 'hidden': !searchExpanded, 'block': searchExpanded }"
    >
        <livewire:trafikrak.storefront.livewire.components.search/>
    </div>
</div>