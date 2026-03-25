<?php

namespace Testa\Storefront\Data;

use Testa\Storefront\Livewire\Account\Forms\AddressForm;

final readonly class AddressData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $company_name,
        public ?string $tax_identifier,
        public int $country_id,
        public ?string $state,
        public string $postcode,
        public string $city,
        public string $line_one,
        public ?string $line_two,
        public bool $shipping_default,
        public bool $billing_default,
    ) {}

    public static function fromForm(AddressForm $form): self
    {
        return new self(
            first_name: $form->first_name,
            last_name: $form->last_name,
            company_name: $form->company_name ?? null,
            tax_identifier: $form->tax_identifier ?? null,
            country_id: $form->country_id,
            state: $form->state,
            postcode: $form->postcode,
            city: $form->city,
            line_one: $form->line_one,
            line_two: $form->line_two ?? null,
            shipping_default: $form->shipping_default,
            billing_default: $form->billing_default,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'tax_identifier' => $this->tax_identifier,
            'country_id' => $this->country_id,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'line_one' => $this->line_one,
            'line_two' => $this->line_two,
            'shipping_default' => $this->shipping_default,
            'billing_default' => $this->billing_default,
        ];
    }
}
