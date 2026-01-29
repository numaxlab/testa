<?php

namespace Testa\Models\Education;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Models\Author;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Education\CourseModuleFactory;
use Testa\Models\Attachment;
use Testa\Models\EventDeliveryMethod;
use Testa\Models\Venue;

class CourseModule extends Model
{
    use HasFactory;
    use HasUrls;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return CourseModuleFactory::new();
    }

    public $translatable = [
        'name',
        'subtitle',
        'description',
        'alert',
    ];
    protected $guarded = [];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this
            ->belongsToMany(Author::class, 'course_module_'.config('lunar.database.table_prefix').'geslib_author')
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'course_module_'.config('lunar.database.table_prefix').'product',
        )->withPivot(['position'])->orderByPivot('position');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'delivery_method' => EventDeliveryMethod::class,
        ];
    }
}
