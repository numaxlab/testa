<?php

namespace Testa\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Education\PriceFactory;

class Price extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return PriceFactory::new();
    }

    public $translatable = [
        'name',
        'description',
    ];
    protected $guarded = [];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
