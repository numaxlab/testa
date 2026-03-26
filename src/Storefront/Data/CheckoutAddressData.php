<?php

namespace Testa\Storefront\Data;

use Testa\Storefront\Livewire\Checkout\Forms\AddressForm;

final readonly class CheckoutAddressData
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $company_name,
        public ?string $tax_identifier,
        public ?string $contact_phone,
        public string $contact_email,
        public int $country_id,
        public ?string $state,
        public string $postcode,
        public string $city,
        public string $line_one,
        public ?string $line_two,
    ) {}

    public static function fromForm(AddressForm $form): self
    {
        return new self(
            first_name: $form->first_name,
            last_name: $form->last_name,
            company_name: $form->company_name,
            tax_identifier: $form->tax_identifier,
            contact_phone: $form->contact_phone,
            contact_email: $form->contact_email,
            country_id: $form->country_id,
            state: $form->state,
            postcode: $form->postcode,
            city: $form->city,
            line_one: $form->line_one,
            line_two: $form->line_two,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'tax_identifier' => $this->tax_identifier,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'country_id' => $this->country_id,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'line_one' => $this->line_one,
            'line_two' => $this->line_two,
        ];
    }
}
