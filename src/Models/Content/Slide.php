<?php

namespace Testa\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\Content\SlideFactory;

class Slide extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    protected static function newFactory()
    {
        return SlideFactory::new();
    }

    public $translatable = [
        'name',
        'description',
        'link',
        'button_text',
    ];
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'section' => Section::class,
        ];
    }
}
