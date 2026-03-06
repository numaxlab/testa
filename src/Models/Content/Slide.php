<?php

namespace Testa\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Content\SlideFactory;
use Testa\Media\StandardMediaDefinitions;

class Slide extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasMedia;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'description',
        'link',
        'button_text',
    ];
    protected $guarded = [];

    protected static function newFactory()
    {
        return SlideFactory::new();
    }

    protected function getDefinitionClass()
    {
        $conversionClasses = config('lunar.media.definitions', []);

        return $conversionClasses['content-slide']
            ?? $conversionClasses[static::class]
            ?? $conversionClasses[get_parent_class(static::class)]
            ?? StandardMediaDefinitions::class;
    }

    protected function casts(): array
    {
        return [
            'section' => Section::class,
        ];
    }
}
