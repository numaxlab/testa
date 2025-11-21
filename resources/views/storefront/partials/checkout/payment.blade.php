<div>
    <ul class="grid gap-5 md:grid-cols-2 mb-5">
        @foreach ($paymentTypes as $type)
            <li>
                <x-numaxlab-atomic::atoms.forms.radio
                        wire:model.live="paymentType"
                        id="paymentType-{{ $type }}"
                        key="paymentType{{ $type }}"
                        name="payment_type"
                        value="{{ $type }}">
                        <span class="text-2xl">
                            {{ __("trafikrak::global.payment_types.{$type}.title") }}
                        </span>
                </x-numaxlab-atomic::atoms.forms.radio>

                <p class="at-small mt-2">
                    {{ __("trafikrak::global.payment_types.{$type}.description") }}
                </p>

                @if ($type === 'direct-debit')
                    @if ($this->paymentType === 'direct-debit')
                        <div class="space-y-6 mt-6">
                            <x-numaxlab-atomic::atoms.input
                                    wire:model="directDebitOwnerName"
                                    type="text"
                                    name="directDebitOwnerName"
                                    id="directDebitOwnerName"
                                    placeholder="{{ __('Nombre completo') }}"
                            >
                                {{ __('Titular de la cuenta') }}
                            </x-numaxlab-atomic::atoms.input>

                            <x-numaxlab-atomic::atoms.input
                                    wire:model="directDebitBankName"
                                    type="text"
                                    name="directDebitBankName"
                                    id="directDebitBankName"
                                    placeholder="{{ __('Entidad bancaria') }}"
                            >
                                {{ __('Entidad bancaria') }}
                            </x-numaxlab-atomic::atoms.input>

                            <x-numaxlab-atomic::atoms.input
                                    wire:model="directDebitIban"
                                    type="text"
                                    name="directDebitIban"
                                    id="directDebitIban"
                                    placeholder="{{ __('ES00 0000 0000 00 0000000000') }}"
                            >
                                {{ __('IBAN') }}
                            </x-numaxlab-atomic::atoms.input>
                        </div>
                    @endif
                @endif
            </li>
        @endforeach
    </ul>

    <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('paymentType')"/>
</div>