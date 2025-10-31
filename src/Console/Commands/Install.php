<?php

namespace Trafikrak\Console\Commands;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Toggle;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Brand;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Tag;
use Trafikrak\Handle;

class Install extends Command
{
    protected $signature = 'lunar:trafikrak:install';

    protected $description = 'Install Trafikrak Lunar based features';

    public function handle(): void
    {
        $this->components->info('Setting up attributes.');

        $this->setupBrandAttributes();

        $this->components->info('Setting up collection attributes.');

        $this->setupCollectionAttributes();

        $this->components->info('Setting up collection groups.');

        $this->setupCollectionGroups();

        $this->components->info('Setting up tags.');

        $this->setupTags();
    }

    private function setupBrandAttributes(): void
    {
        $group = AttributeGroup::create([
            'attributable_type' => Brand::morphName(),
            'name' => collect([
                'es' => 'Editorial',
            ]),
            'handle' => 'editorial',
            'position' => 2,
        ]);

        Attribute::create([
            'attribute_type' => Brand::morphName(),
            'attribute_group_id' => $group->id,
            'position' => 1,
            'handle' => 'in-house',
            'name' => [
                'es' => 'Mostrar en editorial',
            ],
            'description' => [
                'es' => '',
            ],
            'section' => 'main',
            'type' => Toggle::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [
                'richtext' => false,
            ],
            'system' => false,
            'searchable' => false,
        ]);
    }

    private function setupCollectionAttributes(): void
    {
        $group = AttributeGroup::where('handle', 'collection-main')->firstOrFail();

        Attribute::create([
            'attribute_type' => Collection::morphName(),
            'attribute_group_id' => $group->id,
            'position' => 5,
            'handle' => 'is-special',
            'name' => [
                'es' => 'Colección especial (editorial)',
            ],
            'description' => [
                'es' => '',
            ],
            'section' => 'main',
            'type' => Toggle::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [
                'richtext' => false,
            ],
            'system' => false,
            'searchable' => false,
        ]);
    }

    private function setupCollectionGroups(): void
    {
        CollectionGroup::create([
            'name' => 'Destacados editorial',
            'handle' => Handle::COLLECTION_GROUP_EDITORIAL_FEATURED,
        ]);
    }

    private function setupTags(): void
    {
        Tag::create([
            'value' => 'Pedido librería',
        ]);

        Tag::create([
            'value' => 'Subscripción socias',
        ]);

        Tag::create([
            'value' => 'Inscripción cursos',
        ]);

        Tag::create([
            'value' => 'Donación',
        ]);
    }

}
