<div>
    @if ($attachments->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Audiovisual') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="overflow-x-auto">
                <ul class="grid grid-flow-col auto-cols-[55%] gap-6">
                    @foreach ($attachments as $attachment)
                        @can('view', $attachment->media)
                            <li>
                                <x-dynamic-component
                                        :component="'trafikrak::'.$attachment->component_namespace.'.summary'"
                                        :media="$attachment->media"/>
                            </li>
                        @endcan
                    @endforeach
                </ul>
            </div>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</div>