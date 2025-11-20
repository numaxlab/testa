@if ($media->attachments->isNotEmpty())
    <x-numaxlab-atomic::organisms.tier class="mt-10">
        <x-numaxlab-atomic::organisms.tier.header>
            <h2 class="at-heading is-2">
                {{ __('Relacionados') }}
            </h2>
        </x-numaxlab-atomic::organisms.tier.header>

        <ul class="grid gap-6 md:grid-cols-2 mb-10">
            @foreach ($media->attachments as $attachment)
                @if ($attachment->attachable instanceof \Trafikrak\Models\Education\Course)
                    <li>
                        <x-trafikrak::courses.summary :course="$attachment->attachable"/>
                    </li>
                @elseif ($attachment->attachable instanceof \Trafikrak\Models\Education\CourseModule)
                    <li>
                        <x-trafikrak::course-modules.activity :module="$attachment->attachable"/>
                    </li>
                @elseif ($attachment->attachable instanceof \Trafikrak\Models\News\Event)
                    <li>
                        <x-trafikrak::events.summary :event="$attachment->attachable"/>
                    </li>
                @endif
            @endforeach
        </ul>
    </x-numaxlab-atomic::organisms.tier>
@endif