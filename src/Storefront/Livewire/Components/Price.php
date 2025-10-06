<?php

namespace Trafikrak\Storefront\Livewire\Components;

use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\StorefrontSession;
use Trafikrak\Models\Membership\MembershipPlan;

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

        if ($this->purchasable instanceof MembershipPlan) {
            $this->price = $pricing->priceIncTax()->formatted().' / '.$this->purchasable->period();
        }

        return <<<'BLADE'
            <span>
                {{ $price }}
            </span>
            BLADE;
    }
}
