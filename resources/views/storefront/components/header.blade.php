<div
        class="relative"
        x-data="{ menuExpanded: false, searchExpanded: false }"
>
    <div class="container mx-auto px-4">
        <header class="org-site-header border-b-0 lg:gap-10">
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
                    x-data="{ bookshopExpanded: false, editorialExpanded: false, educationExpanded: false, mediaExpanded: false }"
            >
                <div
                        class="lg:flex lg:w-full lg:justify-between relative"
                        :class="bookshopExpanded || editorialExpanded || educationExpanded || mediaExpanded ? 'after:bg-white after:absolute after:top-7 after:right-0 after:z-9 after:h-40 after:lg:w-1/2 after:xl:w-2/3 after:border-b after:border-primary after:shadow-lg' : ''"
                >
                    <ul class="site-header-main-menu">
                        <li
                                @mouseenter="bookshopExpanded = true"
                                @mouseleave="bookshopExpanded = false"
                                class="relative"
                        >
                            <a
                                    href="{{ route('trafikrak.storefront.bookshop.homepage') }}"
                                    wire:navigate
                            >
                                {{ __('Librería') }}
                            </a>

                            <div x-cloak x-show="bookshopExpanded"
                                 class="absolute bg-white top-full -left-3 z-10 px-3 pt-3 pb-8 border-l border-b border-primary min-w-max h-40 flex gap-5 shadow-[-10px_10px_15px_-3px_rgba(0,0,0,0.1)]">
                                @if ($sections->isNotEmpty())
                                    <ul class="grid grid-cols-3 place-content-start gap-x-5">
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
                                @endif

                                <ul>
                                    <li>
                                        <a
                                                href="{{ route('trafikrak.storefront.bookshop.itineraries.index') }}"
                                                wire:navigate
                                        >
                                            {{ __('Itinerarios') }}
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
                            </div>
                        </li>

                        <li
                                @mouseenter="editorialExpanded = true"
                                @mouseleave="editorialExpanded = false"
                                class="relative"
                        >
                            <a
                                    href="{{ route('trafikrak.storefront.editorial.homepage') }}"
                                    wire:navigate
                            >
                                {{ __('Editorial') }}
                            </a>

                            <div x-cloak x-show="editorialExpanded"
                                 class="absolute bg-white top-full -left-3 z-10 px-3 pt-3 pb-8 border-l border-b border-primary min-w-max h-40 flex gap-5 shadow-[-10px_10px_15px_-3px_rgba(0,0,0,0.1)]">
                                @if ($editorialCollections->isNotEmpty())
                                    <ul class="grid grid-cols-2 place-content-start gap-x-5">
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
                                @endif

                                <ul>
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
                                                        href="{{ route('trafikrak.storefront.editorial.collections.special.show', $collection->defaultUrl->slug) }}"
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
                            </div>
                        </li>

                        <li
                                @mouseenter="educationExpanded = true"
                                @mouseleave="educationExpanded = false"
                                class="relative"
                        >
                            <a href="{{ route('trafikrak.storefront.education.homepage') }}" wire:navigate>
                                {{ __('Formación') }}
                            </a>

                            <ul x-cloak x-show="educationExpanded"
                                class="absolute bg-white top-full -left-3 z-10 pl-3 pt-3 pb-8 pr-40 border-l border-b border-primary min-w-max h-40 shadow-[-10px_10px_15px_-3px_rgba(0,0,0,0.1)]">
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
                                    <a href="{{ route('my-courses.index') }}" wire:navigate>
                                        {{ __('Mis cursos') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('trafikrak.storefront.media.homepage') }}" wire:navigate>
                                {{ __('Mediateca') }}
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('trafikrak.storefront.activities.index') }}" wire:navigate>
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