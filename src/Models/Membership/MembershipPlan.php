<?php

namespace Testa\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Lunar\Base\Purchasable;
use Lunar\Base\Traits\HasPrices;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\TaxClass;
use Spatie\LaravelBlink\BlinkFacade as Blink;
use Spatie\Translatable\HasTranslations;

class MembershipPlan extends Model implements Purchasable
{
    use HasTranslations;
    use HasPrices;
    use LogsActivity;

    public const string BILLING_INTERVAL_MONTHLY = 'monthly';
    public const string BILLING_INTERVAL_BIMONTHLY = 'bimonthly';
    public const string BILLING_INTERVAL_QUARTERLY = 'quarterly';
    public const string BILLING_INTERVAL_YEARLY = 'yearly';

    public $translatable = [
        'name',
        'description',
    ];
    protected $guarded = [];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class);
    }

    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::modelClass());
    }

    public function period(): string
    {
        return match ($this->billing_interval) {
            self::BILLING_INTERVAL_MONTHLY => __('mes'),
            self::BILLING_INTERVAL_BIMONTHLY => __('2 meses'),
            self::BILLING_INTERVAL_QUARTERLY => __('trimestre'),
            self::BILLING_INTERVAL_YEARLY => __('año'),
            default => __('mes'),
        };
    }

    public function getFullNameAttribute(): string
    {
        return $this->tier->name.' - '.$this->name;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function getUnitQuantity(): int
    {
        return 1;
    }

    public function getTaxClass(): \Lunar\Models\Contracts\TaxClass
    {
        return Blink::once("tax_class_{$this->tax_class_id}", function () {
            return $this->taxClass;
        });
    }

    public function values()
    {
        return collect();
    }

    public function getTaxReference(): string
    {
        return '';
    }

    public function getType(): string
    {
        return 'digital';
    }

    public function getDescription(): string
    {
        return $this->tier->name;
    }

    public function getOption(): string
    {
        return '';
    }

    public function getIdentifier()
    {
        return $this->name;
    }

    public function isShippable(): bool
    {
        return false;
    }

    public function getThumbnail(): null
    {
        return null;
    }

    public function canBeFulfilledAtQuantity(int $quantity): bool
    {
        return true;
    }

    public function getTotalInventory(): int
    {
        return 1;
    }

    /**
     * WORKAROUND: https://github.com/lunarphp/lunar/pull/2306
     */
    public function getProductAttribute(): object
    {
        return (object) [
            'collections' => new \Illuminate\Database\Eloquent\Collection(),
            'brand_id' => null,
        ];
    }

    /**
     * WORKAROUND: https://github.com/lunarphp/lunar/pull/2306
     */
    public function getOptions(): Collection
    {
        return collect();
    }
}
