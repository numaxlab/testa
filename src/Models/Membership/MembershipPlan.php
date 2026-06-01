<?php

namespace Testa\Models\Membership;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Membership\MembershipPlanFactory;

class MembershipPlan extends Model
{
    use HasFactory;
    use HasTranslations;
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

    protected static function newFactory()
    {
        return MembershipPlanFactory::new();
    }

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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::modelClass(), 'variant_id');
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
        return $this->tier->name . ' - ' . $this->name;
    }

    protected function casts(): array
    {
        return [
            'payment_types' => 'array',
        ];
    }

    /**
     * Return the base price for this plan in cents (integer), sourced from the
     * linked ProductVariant's base price. Returns 0 if no variant or price is
     * configured — the caller is responsible for treating 0 as a config error.
     *
     * Uses getRawOriginal() to bypass Lunar's CastsPrice cast, which is not
     * compatible with PHP 8.4 when the DB returns an integer (preg_replace
     * deprecation on non-string subjects).
     */
    /**
     * Return the Carbon date at which the next billing period ends,
     * calculated from now() according to the plan's billing_interval.
     */
    public function nextExpiresAt(): \Illuminate\Support\Carbon
    {
        return match ($this->billing_interval) {
            self::BILLING_INTERVAL_MONTHLY    => now()->addMonth(),
            self::BILLING_INTERVAL_BIMONTHLY  => now()->addMonths(2),
            self::BILLING_INTERVAL_QUARTERLY  => now()->addMonths(3),
            self::BILLING_INTERVAL_YEARLY     => now()->addYear(),
            default                           => now()->addMonth(),
        };
    }

    public function priceCents(): int
    {
        $price = $this->variant?->basePrices()->first();

        return $price !== null ? (int) $price->getRawOriginal('price') : 0;
    }
}
