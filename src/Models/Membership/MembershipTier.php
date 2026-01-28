<?php

namespace Testa\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\Translatable\HasTranslations;

class MembershipTier extends Model
{
    use HasTranslations;
    use LogsActivity;

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
