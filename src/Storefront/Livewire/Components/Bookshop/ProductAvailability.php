<?php

namespace Trafikrak\Storefront\Livewire\Components\Bookshop;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Base\Purchasable;

class ProductAvailability extends Component
{
    public ?Purchasable $purchasable = null;

    public string $status = '';

    public string $moreInfo = '';

    public function mount(): void
    {
        if (! $this->purchasable) {
            return;
        }

        $stock = (int) ($this->purchasable->stock ?? 0);
        $hasStock = $stock > 0;
        $mode = $this->purchasable->purchasable ?? 'in_stock';

        $this->status = $this->determineStatus($mode, $hasStock);

        if ($hasStock) {
            $byCenter = $this->getStockByCenterData();

            if (! empty($byCenter)) {
                $this->moreInfo = $this->buildStockByCenterInfo($byCenter);
            }
        }
    }

    protected function determineStatus(string $mode, bool $hasStock): string
    {
        return match ($mode) {
            'always' => $hasStock ? __('Disponible') : __('Disponible bajo pedido'),
            default => $hasStock ? __('Disponible') : __('No disponible'),
        };
    }

    protected function getStockByCenterData(): array
    {
        if (! method_exists($this->purchasable, 'translateAttribute')) {
            return [];
        }

        $data = $this->purchasable->translateAttribute('stock-by-center');

        return (array) $data;
    }

    protected function buildStockByCenterInfo(array $data): string
    {
        $lines = [];

        foreach ($data as $center => $quantity) {
            if ($quantity > 0) {
                $lines[] = __('Disponible en :center + Disponible para envío', ['center' => $center]);
            }
        }

        return implode('<br>', $lines);
    }

    public function render(): View
    {
        return view('trafikrak::storefront.livewire.components.bookshop.product-availability');
    }
}
