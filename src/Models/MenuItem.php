<?php

namespace Testa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
    ];
    protected $guarded = [];

    public function children(): HasMany
    {
        return $this
            ->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return match ($this->type) {
            'manual' => $this->link_value,
            'route' => route($this->link_value),
            'model' => $this->linkable?->url,
            default => '#',
        };
    }

    public function getBreadcrumbsAttribute(): string
    {
        $breadcrumbs = [];

        if ($this->parent) {
            $breadcrumbs[] = $this->parent->name;
        }

        $breadcrumbs[] = $this->name;

        return implode(' / ', $breadcrumbs);
    }
}
