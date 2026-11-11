<?php

namespace Testa\Storefront\Livewire\Components\Bookshop;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Models\Collection;
use NumaxLab\Lunar\Geslib\Services\CegalAvailability;

class ProductAvailability extends Component
{
    public ?Purchasable $purchasable = null;

    public string $status = '';

    public string $moreInfo = '';

    public function mount(CegalAvailability $cegalAvailability): void
    {
        if (! $this->purchasable) {
            return;
        }

        $notPurchasableStatus = $this->notPurchasableStatus();

        if ($notPurchasableStatus) {
            $this->status = $notPurchasableStatus->translateAttribute('name') ?: __('No disponible');

            return;
        }

        $byCenter = $this->getStockByCenterData();

        if ($byCenter !== []) {
            $this->status = $this->hasCenterStock($byCenter)
                ? __('Disponible')
                : __('Disponible bajo pedido');

            $this->moreInfo = $this->buildStockByCenterInfo($byCenter);

            return;
        }

        $stock = (int) ($this->purchasable->stock ?? 0);
        $hasStock = $stock > 0;

        $this->status = $this->determineStatus($this->purchasable->purchasable ?? 'in_stock', $hasStock);

        if ($hasStock) {
            return;
        }

        $trustedStockProvider = $cegalAvailability->getAvailability($this->purchasable);

        if ($trustedStockProvider) {
            $this->moreInfo = $trustedStockProvider->delivery_period;
        }
    }

    /**
     * Resolve the Geslib status that forbids buying the product, when there is one.
     */
    protected function notPurchasableStatus(): ?Collection
    {
        if (! method_exists($this->purchasable, 'product')) {
            return null;
        }

        $product = $this->purchasable->product;

        if (! $product || ! method_exists($product, 'notPurchasableStatus')) {
            return null;
        }

        return $product->notPurchasableStatus();
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

    protected function hasCenterStock(array $data): bool
    {
        foreach ($data as $quantity) {
            if ((int) $quantity > 0) {
                return true;
            }
        }

        return false;
    }

    protected function buildStockByCenterInfo(array $data): string
    {
        $centers = (array) config('lunar.geslib.stock_centers', []);
        $lines = [];

        foreach ($data as $center => $quantity) {
            if ((int) $quantity <= 0) {
                continue;
            }

            $centerConfig = $centers[$center] ?? null;

            $lines[] = __('Disponible en :center', [
                'center' => $centerConfig['name'] ?? $center,
            ]);

            if ($centerConfig['ships'] ?? false) {
                $lines[] = __('Disponible para envío');
            }
        }

        return implode('<br>', $lines);
    }

    public function render(): View
    {
        return view('testa::storefront.livewire.components.bookshop.product-availability');
    }
}
