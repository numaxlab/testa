<article class="container mx-auto px-4">
    <h1 class="at-heading is-1">Apoya el proyecto</h1>

    <div class="md:flex gap-6 mt-9">
        <nav class="md:w-1/6">
            <ul>
                <li><a href="">Asóciate</a></li>
                <li><a href="">Haz una donación</a></li>
            </ul>
        </nav>
        <div class="md:w-5/6 lg:w-4/6">
            <p>
                Fusce quis aliquet mauris. Phasellus vel posuere mauris, vel consequat nunc. Donec semper risus sit amet
                eros efficitur sagittis. Aenean metus dui, tincidunt vitae massa sed, sagittis condimentum nibh.
                Phasellus vulputate ullamcorper dui, vitae pulvinar felis placerat et. Etiam vel nibh ante.
            </p>

            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Asóciate') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                <img src="https://picsum.photos/1000/600" alt="" class="mb-4">

                <p>
                    Proin a ex id tortor sodales fermentum. Fusce nisl lacus, hendrerit at placerat sit amet, rhoncus
                    quis augue. Aenean arcu tortor, mollis quis est ut, sodales ultrices nisl. Quisque varius posuere
                    ligula, quis blandit libero porttitor non. Phasellus posuere et orci vitae gravida. Duis pretium
                    eget est quis venenatis. Sed ultrices nec metus porta tempor. Ut eu enim dui. Sed efficitur odio a
                    dapibus laoreet. Fusce ut est at mi lacinia feugiat id et lorem. Cras fringilla eros lectus, a
                    finibus ipsum vestibulum eu. Proin semper finibus eros, eget volutpat dolor efficitur vitae. Nullam
                    venenatis nulla at elit luctus volutpat. Suspendisse eget erat sit amet diam pulvinar pulvinar.
                </p>

                <a
                        href="{{ route('trafikrak.storefront.membership.signup') }}"
                        wire:navigate
                        class="at-button is-primary w-full mt-4"
                >
                    Asóciate
                </a>
            </x-numaxlab-atomic::organisms.tier>

            <x-numaxlab-atomic::organisms.tier class="mt-10">
                <x-numaxlab-atomic::organisms.tier.header>
                    <h2 class="at-heading is-2">
                        {{ __('Haz una aportación') }}
                    </h2>
                </x-numaxlab-atomic::organisms.tier.header>

                <p>
                    Proin a ex id tortor sodales fermentum. Fusce nisl lacus, hendrerit at placerat sit amet, rhoncus
                    quis augue. Aenean arcu tortor, mollis quis est ut, sodales ultrices nisl. Quisque varius posuere
                    ligula, quis blandit libero porttitor non. Phasellus posuere et orci vitae gravida. Duis pretium
                    eget est quis venenatis. Sed ultrices nec metus porta tempor. Ut eu enim dui. Sed efficitur odio a
                    dapibus laoreet. Fusce ut est at mi lacinia feugiat id et lorem. Cras fringilla eros lectus, a
                    finibus ipsum vestibulum eu. Proin semper finibus eros, eget volutpat dolor efficitur vitae. Nullam
                    venenatis nulla at elit luctus volutpat. Suspendisse eget erat sit amet diam pulvinar pulvinar.
                </p>

                <a href="" class="at-button is-primary w-full mt-4">
                    Dona
                </a>
            </x-numaxlab-atomic::organisms.tier>
        </div>
    </div>
</article>