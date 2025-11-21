<?php

namespace Trafikrak\Models\News;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use NumaxLab\Lunar\Geslib\Models\Author;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Models\Attachment;
use Trafikrak\Models\EventDeliveryMethod;
use Trafikrak\Models\Venue;

class Event extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasUrls;
    use HasMedia;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'subtitle',
        'description',
        'alert',
    ];
    protected $guarded = [];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this
            ->belongsToMany(Author::class, 'event_'.config('lunar.database.table_prefix').'geslib_author')
            ->withPivot(['position'])
            ->orderByPivot('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'event_'.config('lunar.database.table_prefix').'product',
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
