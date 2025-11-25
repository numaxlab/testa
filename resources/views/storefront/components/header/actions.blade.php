<ul class="flex gap-5 text-3xl">
    <li>
        @auth
            <a href="{{ route('dashboard') }}" wire:navigate>
                <i class="icon icon-user" aria-hidden="true"></i>
                <span class="text-lg">Carlos</span>
                <span class="sr-only">{{ __('Entrar en perfil') }}</span>
            </a>
        @else
            <a href="{{ route('login') }}" wire:navigate>
                <i class="icon icon-user" aria-hidden="true"></i>
                <span class="sr-only">{{ __('Acceder') }}</span>
            </a>
        @endauth
    </li>
    <li>
        <livewire:trafikrak.storefront.livewire.components.cart lazy/>
    </li>
    <li>
        <button class="text-primary" @click="searchExpanded = !searchExpanded">
            <i class="icon icon-magnifying-glass" aria-hidden="true"></i>
            <span class="sr-only">{{ __('Buscar') }}</span>
        </button>
    </li>
    <li class="site-header-nav-toggle">
        <button
                class="text-primary"
                aria-label="{{ __('Abrir/cerrar menú') }}"
                aria-controls="site-header-nav"
                :aria-expanded="menuExpanded"
                @click="menuExpanded = !menuExpanded"
        >
            <i class="icon icon-bars"
               :class="{ 'icon-bars': !menuExpanded, 'icon-close': menuExpanded }"
               aria-hidden="true"></i>
        </button>
    </li>
</ul>