<?php

namespace Trafikrak\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MembershipTier extends Model
{
    use HasTranslations;

    public $translatable = [
        'name',
        'description',
    ];
    protected $guarded = [];

    public function plans()
    {
        return $this->hasMany(MembershipPlan::class);
    }
}
