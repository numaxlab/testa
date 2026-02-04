<div
        class="relative"
        x-data="{ menuExpanded: false, searchExpanded: false }"
        x-init="$watch('searchExpanded', value => value ? document.querySelector('#globalSearchInput').focus() : null)"
        @click.outside="searchExpanded = false"
>
    <div class="container mx-auto px-4">
        <header class="org-site-header border-b-0 lg:gap-10">
            <a class="w-20 h-7.2 lg:w-30 lg:h-12" href="{{ route('testa.storefront.homepage') }}" wire:navigate>
                <x-testa::logo/>
            </a>

            <div class="lg:hidden">
                <x-testa::header.actions/>
            </div>

            <nav
                    id="site-header-nav"
                    class="site-header-nav lg:flex lg:flex-col-reverse lg:grow"
                    :class="{ 'block': menuExpanded }"
            >
                <div
                        class="lg:flex lg:w-full lg:justify-between relative"
                >
                    <ul class="site-header-main-menu">
                        @foreach ($menuItems as $menuItem)
                            <li
                                    @if ($menuItem->children->isNotEmpty())
                                        x-data="{ submenuExpanded: false }"
                                    @mouseenter="submenuExpanded = true"
                                    @mouseleave="submenuExpanded = false"
                                    class="relative"
                                    @endif
                            >
                                <a
                                        href="{{ $menuItem->url }}"
                                        wire:navigate
                                >
                                    {{ $menuItem->name }}
                                </a>

                                @if ($menuItem->children->isNotEmpty())
                                    <div x-cloak x-show="submenuExpanded"
                                         class="absolute bg-white top-full -left-3 z-10 px-3 pt-3 pb-8 border-l border-b border-primary min-w-max h-40 flex gap-5 shadow-lg">
                                        <ul class="grid grid-cols-3 place-content-start gap-x-5">
                                            @foreach($menuItem->children as $childMenuItem)
                                                <li>
                                                    <a
                                                            href="{{ $childMenuItem->url }}"
                                                            wire:navigate
                                                    >
                                                        {{ $childMenuItem->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="hidden lg:block lg:relative">
                        <x-testa::header.actions/>
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
        <livewire:testa.storefront.livewire.components.search/>
    </div>
</div>