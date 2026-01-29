<?php

namespace Testa\Models\Membership;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\CustomerGroup;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Membership\BenefitFactory;

class Benefit extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return BenefitFactory::new();
    }

    public const string CREDIT_PAYMENT_TYPE = 'credit_payment_type';
    public const string CUSTOMER_GROUP = 'customer_group';

    public $translatable = [
        'name',
    ];
    protected $guarded = [];

    public function membershipPlans(): BelongsToMany
    {
        return $this->belongsToMany(MembershipPlan::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::modelClass());
    }
}
