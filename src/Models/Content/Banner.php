<?php

namespace Trafikrak\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Trafikrak\Database\Factories\Content\BannerFactory;

class Banner extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;
    use HasMedia;

    public $translatable = [
        'name',
        'description',
        'link',
        'button_text',
    ];
    protected $guarded = [];

    protected $casts = [
        'type' => BannerType::class,
    ];

    protected static function newFactory()
    {
        return BannerFactory::new();
    }
}
