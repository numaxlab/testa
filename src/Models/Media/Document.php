<?php

namespace Testa\Models\Media;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Base\Traits\Searchable;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Media\DocumentFactory;
use Testa\Models\Attachment;
use Testa\Policies\MediaPolicy;

#[UsePolicy(MediaPolicy::class)]
class Document extends Model implements Media
{
    use HasFactory;
    use HasTranslations;
    use HasUrls;
    use LogsActivity;
    use Searchable;

    protected static function newFactory()
    {
        return DocumentFactory::new();
    }

    public $translatable = [
        'name',
        'description',
    ];

    protected $guarded = [];

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'media');
    }

    protected function casts(): array
    {
        return [
            'visibility' => Visibility::class,
        ];
    }
}
