<x-filament-panels::page>
    <div
            x-data="{
            copied: null,
            copy(text, id) {
                navigator.clipboard.writeText(text).then(() => {
                    this.copied = id;
                    setTimeout(() => this.copied = null, 2000);
                });
            }
        }"
            class="space-y-6"
    >
        @foreach ($this->getRouteGroups() as $section => $routes)
            <x-filament::section :heading="ucfirst($section)">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="pb-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400 w-1/3">{{ __('Nombre') }}</th>
                            <th class="pb-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400 w-1/2">{{ __('URI') }}</th>
                            <th class="pb-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Métodos') }}</th>
                            <th class="pb-2 text-left font-medium text-gray-500 dark:text-gray-400">{{ __('Copiar URL') }}</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($routes as $route)
                            <tr class="group">
                                <td class="py-2 pr-4 font-mono text-xs text-gray-700 dark:text-gray-300">
                                    {{ $route['name'] }}
                                </td>
                                <td class="py-2 pr-4">
                                    <span class="font-mono text-xs text-primary-600 dark:text-primary-400">{{ $route['uri'] }}</span>
                                    @if ($route['model'])
                                        <span class="ml-2 inline-block rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                                {{ $route['model'] }}
                                            </span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4">
                                    @foreach ($route['methods'] as $method)
                                        <span class="inline-block rounded px-1.5 py-0.5 text-xs font-semibold
                                                {{ $method === 'GET' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                                {{ $method }}
                                            </span>
                                    @endforeach
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-1">
                                        <button
                                                type="button"
                                                x-on:click="copy('{{ $route['url'] }}', '{{ $route['name'] }}')"
                                                class="rounded px-2 py-1 text-xs text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 dark:text-gray-400 transition-colors"
                                                :title="copied === '{{ $route['name'] }}' ? '{{ __('Copiado') }}' : '{{ __('Copiar URL') }}'"
                                        >
                                                <span x-show="copied !== '{{ $route['name'] }}'">
                                                    <x-heroicon-o-clipboard-document class="h-4 w-4"/>
                                                </span>
                                            <span x-show="copied === '{{ $route['name'] }}'" class="text-green-500">
                                                    <x-heroicon-o-check class="h-4 w-4"/>
                                                </span>
                                        </button>
                                        <a
                                                href="{{ $route['url'] }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="rounded px-2 py-1 text-xs text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5 dark:text-gray-400 transition-colors"
                                                title="{{ __('Abrir en nueva pestaña') }}"
                                        >
                                            <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4"/>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
