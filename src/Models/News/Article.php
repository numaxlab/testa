<?php

namespace Testa\Models\News;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\LogsActivity;
use Lunar\Models\Product;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\News\ArticleFactory;
use Testa\Media\StandardMediaDefinitions;

class Article extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasUrls;
    use HasMedia;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
        'summary',
        'content',
    ];
    protected $guarded = [];

    protected static function newFactory()
    {
        return ArticleFactory::new();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::modelClass(),
            'article_'.config('lunar.database.table_prefix').'product',
        )->withPivot(['position'])->orderByPivot('position');
    }

    protected function getDefinitionClass()
    {
        $conversionClasses = config('lunar.media.definitions', []);

        return $conversionClasses['news-article']
            ?? $conversionClasses[static::class]
            ?? $conversionClasses[get_parent_class(static::class)]
            ?? StandardMediaDefinitions::class;
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
