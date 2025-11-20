<?php

namespace Trafikrak\Models\News;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\Translatable\HasTranslations;

class Article extends Model
{
    use HasFactory;
    use HasUrls;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'summary',
        'content',
    ];
    protected $guarded = [];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'article_'.config('lunar.database.table_prefix').'product',
        )->withPivot(['position'])->orderByPivot('position');
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
