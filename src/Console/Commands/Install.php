<?php

namespace Testa\Console\Commands;

use Illuminate\Console\Command;
use Lunar\FieldTypes\File;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\Toggle;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\Brand;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Currency;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\Tag;
use Lunar\Models\TaxClass;
use Testa\Console\Commands\Concerns\ConfiguresFrontendAssets;
use Testa\Console\Commands\Concerns\ConfiguresLivewire;
use Testa\Console\Commands\Concerns\ConfiguresLunarMedia;
use Testa\Console\Commands\Concerns\ConfiguresLunarSearch;
use Testa\Console\Commands\Concerns\ConfiguresUserModel;
use Testa\Console\Commands\Concerns\InstallsNpmPackages;
use Testa\Handle;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Livewire\Membership\DonatePage;

class Install extends Command
{
    use ConfiguresFrontendAssets;
    use ConfiguresLivewire;
    use ConfiguresLunarMedia;
    use ConfiguresLunarSearch;
    use ConfiguresUserModel;
    use InstallsNpmPackages;

    protected $signature = 'lunar:testa:install
                            {--force : Overwrite existing files without confirmation}
                            {--skip-npm : Skip npm package installation}
                            {--skip-frontend : Skip frontend assets configuration}
                            {--skip-data : Skip database setup (attributes, tags, products)}';

    protected $description = 'Install Testa Lunar based features';

    public function handle(): void
    {
        $this->components->info('Installing Testa...');

        $this->configureUserModel();
        $this->configureLivewire();
        $this->configureLunarSearch();
        $this->configureLunarMedia();
        $this->installNpmPackages();
        $this->configureFrontendAssets();

        if (! $this->option('skip-data')) {
            $this->setupDatabaseData();
        } else {
            $this->components->info('Skipping database setup.');
        }

        $this->components->info('Testa installation complete!');
        $this->newLine();
        $this->components->info('Next steps:');
        $this->components->bulletList([
            'Run migrations: php artisan migrate',
            'Build frontend assets: npm run build',
            'Clear caches: php artisan optimize:clear',
        ]);
    }

    private function setupDatabaseData(): void
    {
        $this->components->info('Setting up attributes.');

        $this->setupBrandAttributes();

        $this->components->info('Setting up collection attributes.');

        $this->setupCollectionAttributes();

        $this->components->info('Setting up product attributes.');

        $this->setupProductAttributes();

        $this->components->info('Setting up collection groups.');

        $this->setupCollectionGroups();

        $this->components->info('Setting up tags.');

        $this->setupTags();

        $this->components->info('Setting up donation product.');

        $this->setupDonationProduct();

        $this->components->info('Setting up education product.');

        $this->setupEducationProduct();

        $this->components->info('Setting up membership product.');

        $this->setupMembershipProduct();
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

    private function setupProductAttributes(): void
    {
        $attachmentsGroup = AttributeGroup::create([
            'attributable_type' => Product::morphName(),
            'name' => collect([
                'es' => 'Anexos',
            ]),
            'handle' => 'book-attachments',
            'position' => 4,
        ]);

        Attribute::create([
            'attribute_type' => Product::morphName(),
            'attribute_group_id' => $attachmentsGroup->id,
            'position' => 1,
            'handle' => 'card',
            'name' => [
                'es' => 'Ficha',
            ],
            'description' => [
                'es' => '',
            ],
            'section' => 'main',
            'type' => File::class,
            'required' => true,
            'default_value' => null,
            'configuration' => [
                'multiple' => false,
                'max_files' => null,
                'min_files' => null,
                'file_types' => [],
            ],
            'system' => false,
            'searchable' => true,
        ]);

        Attribute::create([
            'attribute_type' => Product::morphName(),
            'attribute_group_id' => $attachmentsGroup->id,
            'position' => 2,
            'handle' => 'digital-book',
            'name' => [
                'es' => 'Libro digital',
            ],
            'description' => [
                'es' => '',
            ],
            'section' => 'main',
            'type' => File::class,
            'required' => true,
            'default_value' => null,
            'configuration' => [
                'multiple' => false,
                'max_files' => null,
                'min_files' => null,
                'file_types' => [],
            ],
            'system' => false,
            'searchable' => true,
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

    private function setupDonationProduct(): void
    {
        $type = ProductType::create([
            'name' => 'Donación',
        ]);

        $type->mappedAttributes()->attach(
            Attribute::whereAttributeType(Product::morphName())
                ->where('handle', 'name')
                ->get()
                ->pluck('id'),
        );

        $quantityOption = ProductOption::create([
            'handle' => 'donation-quantity',
            'name' => [
                'es' => 'Cantidades predefinidas',
            ],
            'label' => [
                'es' => 'Cantidades predefinidas',
            ],
            'shared' => false,
        ]);

        $quantityValues = $quantityOption->values()->createMany([
            [
                'name' => [
                    'es' => '5',
                ],
                'position' => 1,
            ],
            [
                'name' => [
                    'es' => '10',
                ],
                'position' => 2,
            ],
            [
                'name' => [
                    'es' => '25',
                ],
                'position' => 3,
            ],
        ]);

        $product = Product::create([
            'product_type_id' => $type->id,
            'status' => 'published',
            'attribute_data' => [
                'name' => new Text('Donación'),
            ],
        ]);

        $product->productOptions()->attach($quantityOption->id, ['position' => 1]);

        $taxClass = TaxClass::where('name', 'LIKE', 'Sin IVA')->firstOrFail();
        $currency = Currency::where('default', true)->firstOrFail();

        foreach ($quantityValues as $key => $value) {
            $sku = DonatePage::DONATION_PRODUCT_SKU;
            if ($key > 0) {
                $sku .= '-'.$key;
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'tax_class_id' => $taxClass->id,
                'sku' => $sku,
                'shippable' => false,
                'unit_quantity' => 1,
                'min_quantity' => 1,
                'quantity_increment' => 1,
                'backorder' => 0,
                'purchasable' => 'always',
            ]);

            $variant->values()->attach($value);

            $variant->prices()->create([
                'price' => $value->translateAttribute('name') * 100,
                'currency_id' => $currency->id,
            ]);
        }
    }

    private function setupEducationProduct(): void
    {
        $type = ProductType::create([
            'name' => 'Curso',
        ]);

        $type->mappedAttributes()->attach(
            Attribute::whereAttributeType(Product::morphName())
                ->whereIn('handle', ['name', 'subtitle'])
                ->get()
                ->pluck('id'),
        );

        ProductOption::create([
            'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
            'name' => [
                'es' => 'Tarifas de cursos',
            ],
            'label' => [
                'es' => 'Tarifas de cursos',
            ],
            'shared' => true,
        ]);

        $group = AttributeGroup::create([
            'attributable_type' => ProductVariant::morphName(),
            'name' => collect([
                'es' => 'Datos de variante de curso',
            ]),
            'handle' => 'course-variant-main',
            'position' => 1,
        ]);

        $attribute = Attribute::create([
            'attribute_type' => ProductVariant::morphName(),
            'attribute_group_id' => $group->id,
            'position' => 1,
            'handle' => 'description',
            'name' => [
                'es' => 'Descripción',
            ],
            'description' => [
                'es' => '',
            ],
            'section' => 'main',
            'type' => TranslatedText::class,
            'required' => false,
            'default_value' => null,
            'configuration' => [
                'richtext' => true,
            ],
            'system' => false,
            'searchable' => false,
        ]);

        $type->mappedAttributes()->attach($attribute->id);
    }

    private function setupMembershipProduct(): void
    {
        $type = ProductType::create([
            'name' => 'Membresía',
        ]);

        $type->mappedAttributes()->attach(
            Attribute::whereAttributeType(Product::morphName())
                ->whereIn('handle', ['name'])
                ->get()
                ->pluck('id'),
        );
    }
}
