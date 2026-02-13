<div class="bg-black text-white mt-20">
    <footer class="container mx-auto px-4 pt-10 pb-40">
        <div class="flex gap-10 flex-wrap justify-between">
            <div>
                <p class="at-small">
                    Este proyecto ha sido financiado por la Unión Europea.<br>
                    Next Generation, Plan de Recuperación, Ministerio de Trabajo y Economía Social.
                </p>

                <ul class="flex flex-wrap gap-6 mt-6">
                    <li>
                        <a href="https://next-generation-eu.europa.eu/" target="_blank"
                           class="text-white block h-10 lg:h-14">
                            @include('testa::logos.eu-next-generation')
                        </a>
                    </li>
                    <li>
                        <a href="https://www.mites.gob.es/" target="_blank" class="text-white block h-10 lg:h-14">
                            @include('testa::logos.ministerio-tes')
                        </a>
                    </li>
                    <li>
                        <a href="https://planderecuperacion.gob.es/" target="_blank"
                           class="text-white block h-10 lg:h-14">
                            @include('testa::logos.prtr')
                        </a>
                    </li>
                </ul>
            </div>

            @if ($menuItems->isNotEmpty())
                <ul>
                    @foreach ($menuItems as $menuItem)
                        <li class="mb-2">
                            <a href="{{ $menuItem->url }}" wire:navigate class="text-white hover:text-secondary">
                                {{ $menuItem->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </footer>
</div>