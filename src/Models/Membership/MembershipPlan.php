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

    protected static function newFactory()
    {
        return MembershipPlanFactory::new();
    }

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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::modelClass());
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
}
