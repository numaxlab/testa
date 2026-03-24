<div>
    <x-numaxlab-atomic::atoms.forms.checkbox
            wire:model="privacy_policy"
            name="privacy_policy"
            value="1"
            id="privacy-policy"
    >
        <span class="text-lg">{{ __('Acepto la política de privacidad') }}</span>
    </x-numaxlab-atomic::atoms.forms.checkbox>

    <x-numaxlab-atomic::atoms.forms.input-error :messages="$errors->get('privacy_policy')"/>

    @if ($privacyPolicyText = app(\Testa\Settings\TextSettings::class)->getPrivacyPolicyText())
        <div class="at-small mt-2 prose">
            {!! $privacyPolicyText !!}
        </div>
    @endif
</div>