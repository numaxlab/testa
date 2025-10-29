<article>
    <header class="container mx-auto px-4 mb-10">
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="">
                    Miga
                </a>
            </li>
            <li>
                <a href="">
                    De pan
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">Kitchen Sink</h1>
    </header>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Tipografía</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="md:pr-[30%]">
                <h1 class="at-heading is-1 mb-5">Título 1 [at-heading is-1]</h1>
                <h2 class="at-heading is-2 mb-5">Título 2 [at-heading is-2]</h2>
                <h3 class="at-heading is-3 mb-5">Título 3 [at-heading is-3]</h3>
                <h4 class="at-heading is-4">Título 4 [at-heading is-4]</h4>

                <div class="mt-5">
                    <div class="mb-5">
                        <p>
                            [body]<br>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis ex
                            malesuada
                            pellentesque. Suspendisse mauris mauris, ultricies id egestas.
                        </p>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis ex
                            malesuada pellentesque. <a href="">Suspendisse mauris mauris</a>, ultricies id egestas.
                        </p>
                    </div>

                    <div class="at-lead mb-5">
                        <p>
                            [at-lead]<br>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec viverra eros quis ex
                            malesuada <a href="">pellentesque</a>. Suspendisse mauris mauris, ultricies id egestas.
                        </p>
                    </div>

                    <blockquote class="mb-5">
                        [at-blockquote]<br>
                        Suspendisse mauris mauris, ultricies id egestas.
                    </blockquote>

                    <small class="at-small">
                        [at-small]<br>
                        Lorem ipsum dolor sit amet, sectetur ipsum do amet, consectetur. Lorem ipsum dolor sit amet,
                        consectetur amet ipsum.
                    </small>
                </div>
            </div>
        </x-numaxlab-atomic::organisms.tier>

        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Colores</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="flex gap-4">
                <div class="p-4 bg-primary">primary</div>
                <div class="p-4 bg-secondary">secondary</div>
                <div class="p-4 bg-success">success</div>
                <div class="p-4 bg-warning">warning</div>
                <div class="p-4 bg-danger">danger</div>
            </div>
        </x-numaxlab-atomic::organisms.tier>

        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Libros</h2>
                <a href="" class="at-small">
                    ver más
                </a>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($products as $product)
                    <li>
                        <x-trafikrak::products.summary
                                :product="$product"
                                :href="route('trafikrak.storefront.bookshop.products.show', $product->defaultUrl->slug)"
                        />
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>

    <div class="mb-10">
        <div class="container mx-auto px-4 mb-5">
            <h2 class="at-heading is-2">
                Banners
            </h2>
        </div>

        <x-trafikrak::banners.contained :banner="$banner"/>

        <x-trafikrak::banners.full-width :banner="$banner"/>
    </div>

    <div class="container mx-auto px-4">
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">Cursos</h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($courses as $course)
                    <li>
                        <x-trafikrak::courses.summary :course="$course"/>
                    </li>
                @endforeach
            </ul>
        </x-numaxlab-atomic::organisms.tier>
    </div>
</article>