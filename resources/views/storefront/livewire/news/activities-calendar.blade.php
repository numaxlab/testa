<article class="container mx-auto px-4">
    <header>
        <x-numaxlab-atomic::molecules.breadcrumb :label="__('Miga de pan')">
            <li>
                <a href="{{ route('testa.storefront.news.homepage') }}" wire:navigate>
                    {{ __('Actualidad') }}
                </a>
            </li>
        </x-numaxlab-atomic::molecules.breadcrumb>

        <h1 class="at-heading is-1">
            {{ __('Actividades') }}
            <a href="{{ route('testa.storefront.activities.index') }}" wire:navigate class="at-small">
                {{ __('Ver lista') }}
            </a>
        </h1>
    </header>

    <div class="my-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button wire:click="previousMonth"
                    class="at-button">
                <i class="icon icon-arrow-left" aria-hidden="true"></i>
            </button>

            <span class="text-lg font-bold min-w-48 text-center">
                {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
            </span>

            <button wire:click="nextMonth" class="at-button">
                <i class="icon icon-arrow-right" aria-hidden="true"></i>
            </button>
        </div>

        <div>
            <x-numaxlab-atomic::atoms.forms.select
                    wire:model.live="t"
                    name="t"
                    aria-label="{{ __('Filtrar por tipo') }}"
            >
                <option value="">{{ __('Todos los tipos') }}</option>
                <option value="c">{{ __('Sesiones de cursos') }}</option>
                @foreach ($eventTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </x-numaxlab-atomic::atoms.forms.select>
        </div>
    </div>

    <div wire:key="calendar-{{ $year }}-{{ $month }}-{{ $t }}"
         x-data="calendarGrid(
        {{ $year }},
        {{ $month }},
        @js($activitiesByDay)
    )">
        <div class="grid grid-cols-7 text-center">
            <template x-for="dayName in dayNames" :key="dayName">
                <div class="bg-primary text-white py-2 font-semibold text-sm" x-text="dayName"></div>
            </template>
        </div>

        <div class="grid grid-cols-7 border-t border-l border-gray-200">
            <template x-for="cell in cells" :key="cell.key">
                <div
                        class="border-b border-r p-1 transition relative sm:min-h-[120px]"
                        @click="cell.currentMonth && cell.activities.length > 0 && (selectedDay = selectedDay === cell.day ? null : cell.day)"
                        :class="{
                        'bg-gray-50 text-gray-400': !cell.currentMonth,
                        'bg-white hover:bg-gray-50 cursor-pointer': cell.currentMonth,
                        'ring-2 ring-primary ring-inset': cell.isToday,
                        'bg-primary/10': selectedDay === cell.day,
                    }"
                >
                    <div class="text-right text-sm font-bold mb-1 pr-1"
                         :class="cell.isToday ? 'text-primary' : (cell.currentMonth ? 'text-gray-800' : 'text-gray-400')"
                         x-text="cell.day"
                    ></div>

                    <template x-if="cell.currentMonth && cell.activities.length > 0">
                        <div>
                            <template x-for="activity in cell.activities" :key="activity.title + activity.time">
                                <a
                                        :href="activity.url"
                                        @click.stop
                                        class="hidden-xs block mt-1 p-1 rounded text-left truncate text-xs font-medium transition bg-primary/15 text-primary hover:bg-primary/25"
                                >
                                    <span class="font-bold" x-text="activity.time"></span>
                                    <span x-text="activity.title"></span>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="selectedDay !== null" x-transition
             class="mt-4 bg-white p-4 shadow-lg border-l-4 border-primary">
            <h3 class="text-xl font-bold text-gray-800 mb-3">
                {{ __('Actividades del día') }} <span x-text="selectedDay"></span>
            </h3>
            <div class="space-y-2">
                <template x-for="activity in selectedDayActivities" :key="activity.title + activity.time">
                    <a
                            :href="activity.url"
                            class="block p-3 bg-primary/10 hover:bg-primary/20 transition shadow-sm"
                    >
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-sm text-gray-800" x-text="activity.title"></span>
                            <span class="text-xs font-mono text-gray-600" x-text="activity.time"></span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1" x-text="activity.venue || activity.type_label"></p>
                    </a>
                </template>
            </div>
            <button @click="selectedDay = null" class="mt-4 w-full at-button is-primary">
                {{ __('Cerrar') }}
            </button>
        </div>
    </div>

</article>

<script>
    function calendarGrid (year, month, activitiesByDay) {
        return {
            dayNames: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            selectedDay: null,
            activitiesByDay: activitiesByDay,

            get cells () {
                const cells = [];
                const daysInMonth = new Date(year, month, 0).getDate();
                let firstDay = new Date(year, month - 1, 1).getDay();
                firstDay = firstDay === 0 ? 6 : firstDay - 1; // Monday = 0

                const prevMonthDays = new Date(year, month - 1, 0).getDate();

                const today = new Date();
                const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month - 1;

                for (let i = firstDay; i > 0; i--) {
                    cells.push({
                        key: 'prev-' + i,
                        day: prevMonthDays - i + 1,
                        currentMonth: false,
                        isToday: false,
                        activities: [],
                    });
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    cells.push({
                        key: 'day-' + d,
                        day: d,
                        currentMonth: true,
                        isToday: isCurrentMonth && today.getDate() === d,
                        activities: this.activitiesByDay[d] || [],
                    });
                }

                const remaining = (7 - (cells.length % 7)) % 7;
                for (let i = 1; i <= remaining; i++) {
                    cells.push({
                        key: 'next-' + i,
                        day: i,
                        currentMonth: false,
                        isToday: false,
                        activities: [],
                    });
                }

                return cells;
            },

            get selectedDayActivities () {
                if (this.selectedDay === null) return [];
                return this.activitiesByDay[this.selectedDay] || [];
            },
        };
    }
</script>
