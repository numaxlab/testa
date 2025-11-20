<x-filament-widgets::widget>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-ta-header flex flex-col gap-3 p-4 sm:px-6 sm:flex-row sm:items-center">
            <div class="grid gap-y-1">
                <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ __('lunarpanel::productoption.widgets.product-options.variants-table.title') }}
                </h3>
            </div>
        </div>
        <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
            @if(count($this->variants))
                <x-filament-tables::table>
                    <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr class="bg-gray-50 dark:bg-white/5">
                        @if($this->hasNewVariants)
                            <x-filament-tables::header-cell>
                            </x-filament-tables::header-cell>
                        @endif
                        <x-filament-tables::header-cell
                                class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                      <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('lunarpanel::productoption.widgets.product-options.variants-table.table.option.label') }}
                      </span>
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                      <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('lunarpanel::productoption.widgets.product-options.variants-table.table.sku.label') }}
                      </span>
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                      <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('lunarpanel::productoption.widgets.product-options.variants-table.table.price.label') }}
                      </span>
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                      <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                        {{ __('lunarpanel::productvariant.form.tax_class_id.label') }}
                      </span>
                        </x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>
                        </x-filament-tables::header-cell>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">

                    @foreach($this->variants as $permutationIndex => $permutation)
                        <x-filament-tables::row wire:key="permutation_{{ $permutation['key'] }}">
                            @if($this->hasNewVariants)
                                <x-filament-tables::cell class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                    <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                        @if(!$permutation['variant_id'])
                                            <x-filament::badge color="info">
                                                {{ __('lunarpanel::productoption.widgets.product-options.variants-table.table.new.label') }}
                                            </x-filament::badge>
                                        @endif
                                    </div>
                                </x-filament-tables::cell>
                            @endif
                            <x-filament-tables::cell>
                                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                        <span class="fi-ta-text-item-label flex flex-col text-sm leading-6 text-gray-950 dark:text-white">
                          @foreach($permutation['values'] as $option => $value)
                                <small><strong>{{ $option }}:</strong> {{ $value }}</small>
                            @endforeach
                        </span>
                                </div>
                            </x-filament-tables::cell>
                            <x-filament-tables::cell class="w-32">
                                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                    <span class="text-sm"><small>{{ $permutation['sku'] }}</small></span>
                                </div>
                            </x-filament-tables::cell>
                            <x-filament-tables::cell class="w-32">
                                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                                type="text"
                                                wire:model="variants.{{ $permutationIndex }}.price"
                                        />
                                    </x-filament::input.wrapper>
                                </div>
                            </x-filament-tables::cell>
                            <x-filament-tables::cell class="w-34">
                                <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select
                                                wire:model="variants.{{ $permutationIndex }}.tax_class_id">
                                            @foreach ($taxClasses as $taxClass)
                                                <option
                                                        value="{{ $taxClass->id }}"
                                                        {{ $permutation['tax_class_id'] == $taxClass->id ? 'selected' : '' }}
                                                        wire:key="permutation_{{ $permutation['key'] }}_tax_class_{{ $taxClass->id }}"
                                                >
                                                    {{ $taxClass->name }}
                                                </option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </div>
                            </x-filament-tables::cell>
                            <x-filament-tables::cell>
                                <div class="flex items-center space-x-2">
                                    @if($permutation['variant_id'])
                                        <x-filament::link :href="$this->getVariantLink($permutation['variant_id'])">
                                            {{ __('lunarpanel::productoption.widgets.product-options.variants-table.actions.edit.label') }}
                                        </x-filament::link>
                                    @endif
                                    <button type="button" wire:click="removeVariant('{{ $permutationIndex }}')"
                                            class="text-red-500 font-semibold text-sm hover:underline">
                                        {{ __('lunarpanel::productoption.widgets.product-options.variants-table.actions.delete.label') }}
                                    </button>
                                </div>
                            </x-filament-tables::cell>

                        </x-filament-tables::row>
                    @endforeach
                    </tbody>
                </x-filament-tables::table>
            @else
                <x-filament-tables::empty-state
                        :heading="__('lunarpanel::productoption.widgets.product-options.variants-table.empty.heading')"
                        icon="lucide-shapes"></x-filament-tables::empty-state>
            @endif
        </div>
    </div>

    <div class="mt-4 flex">
        {{ $this->saveVariantsAction }}
    </div>
</x-filament-widgets::widget>