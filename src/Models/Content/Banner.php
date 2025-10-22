<?php

namespace Trafikrak\Models\Content;

use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Banner extends Model
{
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
}
