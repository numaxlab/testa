<?php

namespace Testa\Storefront\Livewire\Components;

use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\StorefrontSession;

class Price extends Component
{
    public ?Purchasable $purchasable = null;

    public ?string $price = null;

    public function render(): string
    {
        $pricing = $this->purchasable
            ->pricing()
            ->currency(StorefrontSession::getCurrency())
            ->customerGroups(StorefrontSession::getCustomerGroups())
            ->get()->matched;

        $this->price = $pricing->priceIncTax()->formatted();

        return <<<'BLADE'
            <span>
                {{ $price }}
            </span>
            BLADE;
    }
}
