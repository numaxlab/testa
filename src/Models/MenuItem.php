<?php

namespace Testa\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Lunar\Base\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use Testa\Database\Factories\MenuItemFactory;

class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;

    public $translatable = [
        'name',
    ];
    protected $guarded = [];

    protected static function newFactory()
    {
        return MenuItemFactory::new();
    }

    public function children(): HasMany
    {
        return $this
            ->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('sort_position');
    }

    public function publishedChildren(): HasMany
    {
        return $this
            ->hasMany(MenuItem::class, 'parent_id')
            ->where('is_published', true)
            ->orderBy('sort_position');
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
            'model' => $this->linkable?->url ?? '#',
            'group' => '#',
            default => '#',
        };
    }

    public function getIsGroupAttribute(): bool
    {
        return $this->type === 'group';
    }

    public function getGroupChildrenAttribute(): Collection
    {
        return $this->publishedChildren->filter(fn($item) => $item->is_group);
    }

    public function getLinkChildrenAttribute(): Collection
    {
        return $this->publishedChildren->filter(fn($item) => ! $item->is_group);
    }

    public function getBreadcrumbsAttribute(): string
    {
        $breadcrumbs = [];

        if ($this->parent) {
            if ($this->parent->parent) {
                $breadcrumbs[] = $this->parent->parent->name;
            }
            $breadcrumbs[] = $this->parent->name;
        }

        $breadcrumbs[] = $this->name;

        return implode(' / ', $breadcrumbs);
    }
}
