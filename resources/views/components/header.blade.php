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

                    <div class="hidden lg:block lg:relative">
                        <x-testa::header.actions/>
                    </div>
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
        </header>
    </div>

    <div
            class="absolute inset-0 hidden"
            :class="{ 'hidden': !searchExpanded, 'block': searchExpanded }"
    >
        <livewire:testa.storefront.livewire.components.search/>
    </div>
</div>