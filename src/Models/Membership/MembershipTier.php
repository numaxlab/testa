<?php

namespace Testa\Models\Membership;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Membership\MembershipTierFactory;

class MembershipTier extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return MembershipTierFactory::new();
    }

    public $translatable = [
        'name',
        'description',
    ];
    protected $guarded = [];

    public function plans(): HasMany
    {
        return $this->hasMany(MembershipPlan::class);
    }

    public function purchasable(): BelongsTo
    {
        return $this->belongsTo(Product::modelClass(), 'purchasable_id');
    }
}
