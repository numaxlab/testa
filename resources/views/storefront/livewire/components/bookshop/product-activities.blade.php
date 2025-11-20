<div>
    @if ($activities->isNotEmpty())
        <x-numaxlab-atomic::organisms.tier class="mb-10">
            <x-numaxlab-atomic::organisms.tier.header>
                <h2 class="at-heading is-2">
                    {{ __('Actividades relacionados') }}
                </h2>
            </x-numaxlab-atomic::organisms.tier.header>

            <div class="overflow-x-auto">
                <ul class="grid grid-flow-col auto-cols-[40%] md:auto-cols-[30%] gap-6">
                    @foreach ($activities as $activity)
                        <li>
                            @if ($activity instanceof \Trafikrak\Models\News\Event)
                                <x-trafikrak::events.mini :event="$activity"/>
                            @elseif ($activity instanceof \Trafikrak\Models\Education\CourseModule)
                                <x-trafikrak::course-modules.mini :module="$activity"/>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-numaxlab-atomic::organisms.tier>
    @endif
</div>