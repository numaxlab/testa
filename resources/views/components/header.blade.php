<div
        class="relative"
        x-data="{ menuExpanded: false, searchExpanded: false }"
        x-init="
            $watch('searchExpanded', value => value ? document.querySelector('#globalSearchInput').focus() : null);
            $watch('menuExpanded', value => document.body.classList.toggle('overflow-hidden', value));
        "
        @click.outside="searchExpanded = false"
>
    <div class="container mx-auto px-4">
        <header class="org-site-header border-b-0 lg:gap-10">
            <a class="w-20 h-7.2 lg:w-30 lg:h-12" href="{{ route('testa.storefront.homepage') }}" wire:navigate>
                <x-testa::logo/>
            </a>

            <nav
                    id="site-header-nav"
                    class="site-header-nav lg:flex lg:flex-col-reverse lg:grow"
            >
                <div
                        class="lg:flex lg:w-full relative"
                >
                    <ul class="site-header-main-menu">
                        @foreach ($menuItems as $menuItem)
                            <li
                                    @if ($menuItem->publishedChildren->isNotEmpty())
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

                                @if ($menuItem->publishedChildren->isNotEmpty())
                                    <div x-cloak x-show="submenuExpanded"
                                         class="absolute bg-white top-full -left-3 z-10 pl-3 pr-8 pt-3 pb-8 border-l border-b border-primary min-w-max flex gap-5 shadow-lg">
                                        @foreach($menuItem->groupChildren as $group)
                                            <div>
                                                <h3 class="font-bold mb-2">{{ $group->name }}</h3>
                                                @if ($group->publishedChildren->isNotEmpty())
                                                    <ul>
                                                        @foreach($group->publishedChildren as $groupChild)
                                                            <li>
                                                                <a
                                                                        href="{{ $groupChild->url }}"
                                                                        wire:navigate
                                                                >
                                                                    {{ $groupChild->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach

                                        @if ($menuItem->linkChildren->isNotEmpty())
                                            <ul class="{{ $menuItem->groupChildren->isNotEmpty() ? 'ml-5' : '' }}">
                                                @foreach($menuItem->linkChildren as $childMenuItem)
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
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex mb-5 text-sm">
                    <ul class="flex gap-5 mr-5">
                        <li>
                            <a href="mailto:{{ $contactSettings->email_address }}">
                                {{ $contactSettings->email_address }}
                            </a>
                        </li>
                        <li>
                            <a href="tel:{{ $contactSettings->phone_number }}">
                                {{ $contactSettings->phone_number }}
                            </a>
                        </li>
                    </ul>
                    <ul class="flex gap-2">
                        @if ($contactSettings->instagram_url)
                            <li>
                                <a href="{{ $contactSettings->instagram_url }}">
                                    <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                    <span class="sr-only">Instagram</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->facebook_url)
                            <li>
                                <a href="{{ $contactSettings->facebook_url }}" target="_blank">
                                    <i class="fa-brands fa-facebook" aria-hidden="true"></i>
                                    <span class="sr-only">Facebook</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->youtube_url)
                            <li>
                                <a href="{{ $contactSettings->youtube_url }}" target="_blank">
                                    <i class="fa-brands fa-youtube" aria-hidden="true"></i>
                                    <span class="sr-only">Youtube</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->vimeo_url)
                            <li>
                                <a href="{{ $contactSettings->vimeo_url }}" target="_blank">
                                    <i class="fa-brands fa-vimeo" aria-hidden="true"></i>
                                    <span class="sr-only">Vimeo</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->x_url)
                            <li>
                                <a href="{{ $contactSettings->x_url }}" target="_blank">
                                    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
                                    <span class="sr-only">X/Twitter</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->bluesky_url)
                            <li>
                                <a href="{{ $contactSettings->bluesky_url }}" target="_blank">
                                    <i class="fa-brands fa-bluesky" aria-hidden="true"></i>
                                    <span class="sr-only">Bluesky</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->mastodon_url)
                            <li>
                                <a href="{{ $contactSettings->mastodon_url }}" target="_blank">
                                    <i class="fa-brands fa-mastodon" aria-hidden="true"></i>
                                    <span class="sr-only">Mastodon</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->soundcloud_url)
                            <li>
                                <a href="{{ $contactSettings->soundcloud_url }}" target="_blank">
                                    <i class="fa-brands fa-soundcloud" aria-hidden="true"></i>
                                    <span class="sr-only">Soundcloud</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->telegram_url)
                            <li>
                                <a href="{{ $contactSettings->telegram_url }}" target="_blank">
                                    <i class="fa-brands fa-telegram" aria-hidden="true"></i>
                                    <span class="sr-only">Telegram</span>
                                </a>
                            </li>
                        @endif
                        @if ($contactSettings->whatsapp_url)
                            <li>
                                <a href="{{ $contactSettings->whatsapp_url }}" target="_blank">
                                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                                    <span class="sr-only">Whatsapp</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <x-testa::header.actions/>
        </header>
    </div>

    {{-- Mobile menu overlay --}}
    <div
            class="fixed inset-0 z-50 lg:hidden"
            x-cloak
            x-show="menuExpanded"
            @keydown.escape.window="menuExpanded = false"
    >
        {{-- Backdrop --}}
        <div
                class="absolute inset-0 bg-black/60"
                @click="menuExpanded = false"
                x-show="menuExpanded"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
        ></div>

        {{-- Slide-in panel --}}
        <div
                class="absolute right-0 top-0 h-full w-full bg-white flex flex-col shadow-2xl"
                x-show="menuExpanded"
                x-transition:enter="transition-transform ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition-transform ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
        >
            {{-- Panel header --}}
            <div class="flex justify-between items-center px-5 py-4 border-b border-primary shrink-0">
                <a class="w-20 h-7" href="{{ route('testa.storefront.homepage') }}" wire:navigate
                   @click="menuExpanded = false">
                    <x-testa::logo/>
                </a>
                <button
                        class="text-primary text-2xl leading-none p-1 -mr-1"
                        @click="menuExpanded = false"
                        aria-label="{{ __('Cerrar menú') }}"
                >
                    <i class="icon icon-close" aria-hidden="true"></i>
                </button>
            </div>

            {{-- Nav items --}}
            <nav class="flex-1 overflow-y-auto">
                <ul>
                    @foreach ($menuItems as $menuItem)
                        <li class="border-b border-gray-100"
                            @if ($menuItem->publishedChildren->isNotEmpty()) x-data="{ submenuExpanded: false }" @endif>
                            @if ($menuItem->publishedChildren->isNotEmpty())
                                <button
                                        class="flex items-center justify-between w-full px-5 py-4 text-left font-semibold"
                                        @click="submenuExpanded = !submenuExpanded"
                                >
                                    <span>{{ $menuItem->name }}</span>
                                    <i class="icon text-primary text-sm transition-transform duration-200"
                                       :class="submenuExpanded ? 'icon-arrow-up' : 'icon-arrow-down'"
                                       aria-hidden="true"></i>
                                </button>
                                <div
                                        x-show="submenuExpanded"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-1"
                                        class="bg-gray-50 px-5 pb-4"
                                >
                                    @foreach($menuItem->groupChildren as $group)
                                        <div class="mb-3 pt-3">
                                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">{{ $group->name }}</h3>
                                            @if ($group->publishedChildren->isNotEmpty())
                                                <ul class="flex flex-col gap-1">
                                                    @foreach($group->publishedChildren as $groupChild)
                                                        <li>
                                                            <a
                                                                    href="{{ $groupChild->url }}"
                                                                    wire:navigate
                                                                    @click="menuExpanded = false"
                                                                    class="block text-sm py-1"
                                                            >{{ $groupChild->name }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if ($menuItem->linkChildren->isNotEmpty())
                                        <ul class="flex flex-col gap-1 {{ $menuItem->groupChildren->isNotEmpty() ? 'pt-2' : 'pt-3' }}">
                                            @foreach($menuItem->linkChildren as $childMenuItem)
                                                <li>
                                                    <a
                                                            href="{{ $childMenuItem->url }}"
                                                            wire:navigate
                                                            @click="menuExpanded = false"
                                                            class="block text-sm py-1"
                                                    >{{ $childMenuItem->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @else
                                <a
                                        href="{{ $menuItem->url }}"
                                        wire:navigate
                                        @click="menuExpanded = false"
                                        class="block px-5 py-4 font-semibold"
                                >{{ $menuItem->name }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Contact & social footer --}}
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 shrink-0">
                <div class="flex flex-col gap-1 text-sm mb-3">
                    <a href="mailto:{{ $contactSettings->email_address }}" class="hover:text-primary">
                        <i class="icon icon-mail text-primary mr-1 text-xs" aria-hidden="true"></i>
                        {{ $contactSettings->email_address }}
                    </a>
                    <a href="tel:{{ $contactSettings->phone_number }}" class="hover:text-primary">
                        {{ $contactSettings->phone_number }}
                    </a>
                </div>
                <ul class="flex flex-wrap gap-4 text-lg">
                    @if ($contactSettings->instagram_url)
                        <li>
                            <a href="{{ $contactSettings->instagram_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                                <span class="sr-only">Instagram</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->facebook_url)
                        <li>
                            <a href="{{ $contactSettings->facebook_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-facebook" aria-hidden="true"></i>
                                <span class="sr-only">Facebook</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->youtube_url)
                        <li>
                            <a href="{{ $contactSettings->youtube_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-youtube" aria-hidden="true"></i>
                                <span class="sr-only">Youtube</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->vimeo_url)
                        <li>
                            <a href="{{ $contactSettings->vimeo_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-vimeo" aria-hidden="true"></i>
                                <span class="sr-only">Vimeo</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->x_url)
                        <li>
                            <a href="{{ $contactSettings->x_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
                                <span class="sr-only">X/Twitter</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->bluesky_url)
                        <li>
                            <a href="{{ $contactSettings->bluesky_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-bluesky" aria-hidden="true"></i>
                                <span class="sr-only">Bluesky</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->mastodon_url)
                        <li>
                            <a href="{{ $contactSettings->mastodon_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-mastodon" aria-hidden="true"></i>
                                <span class="sr-only">Mastodon</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->soundcloud_url)
                        <li>
                            <a href="{{ $contactSettings->soundcloud_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-soundcloud" aria-hidden="true"></i>
                                <span class="sr-only">Soundcloud</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->telegram_url)
                        <li>
                            <a href="{{ $contactSettings->telegram_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-telegram" aria-hidden="true"></i>
                                <span class="sr-only">Telegram</span>
                            </a>
                        </li>
                    @endif
                    @if ($contactSettings->whatsapp_url)
                        <li>
                            <a href="{{ $contactSettings->whatsapp_url }}" target="_blank" class="hover:text-primary">
                                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                                <span class="sr-only">Whatsapp</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div
            class="absolute inset-0 hidden"
            :class="{ 'hidden': !searchExpanded, 'block': searchExpanded }"
    >
        <livewire:testa.storefront.livewire.components.search/>
    </div>
</div>