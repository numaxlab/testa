<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Base\Traits\Searchable;
use Lunar\Models\Product;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Database\Factories\Education\CourseFactory;

class Course extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasUrls;
    use HasMedia;
    use HasTranslations;
    use LogsActivity;
    use Searchable;

    public $translatable = [
        'name',
        'subtitle',
        'description',
    ];
    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'delivery_method' => CourseDeliveryMethod::class,
    ];
    protected $guarded = [];

    protected static function newFactory()
    {
        return CourseFactory::new();
    }


    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'course_' . config('lunar.database.table_prefix') . 'product',
        )->withPivot(['position'])->orderByPivot('position');
    }
}
