<?php

namespace Trafikrak\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Base\Traits\Searchable;
use Lunar\Models\Product;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Database\Factories\Education\CourseFactory;
use Trafikrak\Models\Attachment;

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
        'alert',
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

    public function horizontalImage(): MorphOne
    {
        return $this
            ->morphOne(config('media-library.media_model'), 'model')
            ->where('custom_properties->orientation', 'horizontal');
    }

    public function verticalImage(): MorphOne
    {
        return $this
            ->morphOne(config('media-library.media_model'), 'model')
            ->where('custom_properties->orientation', 'vertical');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'course_'.config('lunar.database.table_prefix').'product',
        )->withPivot(['position'])->orderByPivot('position');
    }

    public function purchasable(): BelongsTo
    {
        return $this->belongsTo(Product::modelClass());
    }

    public function variants(): HasMany
    {
        return $this->purchasable ? $this->purchasable->variants() : (new Product)->variants();
    }

    public function thumbnailImage()
    {
        if ($this->horizontalImage) {
            return $this->horizontalImage;
        } else {
            $media = $this->verticalImage;
        }

        return $media('medium');
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

}
