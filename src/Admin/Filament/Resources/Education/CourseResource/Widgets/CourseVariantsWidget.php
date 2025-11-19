<?php

namespace Trafikrak\Admin\Filament\Resources\Education\CourseResource\Widgets;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lunar\Admin\Events\ProductVariantOptionsUpdated;
use Lunar\Admin\Filament\Resources\ProductResource\Widgets\ProductOptionsWidget;
use Lunar\Admin\Filament\Resources\ProductVariantResource;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use NumaxLab\Lunar\Geslib\Admin\Actions\MapVariantsToProductOptionsWithTaxClass;
use Trafikrak\Observers\CourseObserver;

class CourseVariantsWidget extends ProductOptionsWidget
{
    protected static string $view = 'trafikrak::filament.resources.education.course-resource.widgets.course-variants';
    public Collection $taxClasses;

    public function mount(): void
    {
        $this->configureBaseOptions();

        $this->taxClasses = TaxClass::all();
    }

    public function configureBaseOptions(): void
    {
        $productOptions = $this->query()->get();

        $sharedOptionIds = $productOptions->filter(
            fn ($option) => $option->shared,
        )->pluck('id');

        $disabledSharedOptionValues = ProductOptionValue::whereIn(
            'product_option_id',
            $sharedOptionIds,
        )->whereNotIn(
            'id',
            $productOptions->pluck('values')->flatten()->pluck('id'),
        )->get();

        $options = [];

        foreach ($productOptions as $productOption) {
            $values = $productOption->values->count() ? $productOption->values->map(function ($value) {
                return $this->mapOptionValue($value, true);
            })->merge(
                $disabledSharedOptionValues->filter(
                    fn ($value) => $value->product_option_id == $productOption->id,
                )->map(
                    fn ($value) => $this->mapOptionValue($value, false),
                ),
            )->sortBy('position')->values()->toArray() : [];

            $options[] = $this->mapOption($productOption, $values);
        }

        $this->configuredOptions = $options;

        $this->mapVariantPermutations();
    }

    public function query()
    {
        if (! $this->record->purchasable) {
            return ProductOption::where('handle', CourseObserver::RATE_PRODUCT_OPTION_HANDLE)
                ->with('values');
        }

        return $this->record->purchasable
            ->productOptions()
            ->with('values', function ($query) {
                $query->whereHas('variants', function ($relation) {
                    $relation->whereIn($relation->getModel()->getTable().'.id',
                        $this->record->purchasable->variants()->pluck('id'));
                });
            });
    }

    public function mapVariantPermutations($fillMissing = true): void
    {
        $optionValues = collect($this->configuredOptions)
            ->filter(
                fn ($option) => $option['value'],
            )
            ->mapWithKeys(
                fn ($option)
                    => [
                    $option['value'] => collect($option['option_values'])
                        ->filter(
                            fn ($value) => $fillMissing ? true : $value['enabled'],
                        )
                        ->map(
                            fn ($value) => $value['value'],
                        ),
                ],
            )->toArray();

        $variants = $this->record->variants->load([
            'taxClass', 'basePrices.currency', 'basePrices.priceable', 'values.option',
        ])->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->basePrices->first()?->price->decimal ?: 0,
                'stock' => $variant->stock,
                'tax_class_id' => $variant->taxClass->id,
                'values' => $variant->values->mapWithKeys(
                    fn ($value) => [$value->option->translate('name') => $value->translate('name')],
                )->toArray(),
            ];
        })->toArray();

        $this->variants = MapVariantsToProductOptionsWithTaxClass::map($optionValues, $variants, $fillMissing);
    }

    public function saveVariantsAction()
    {
        return Action::make('saveVariants')
            ->action(function () {
                DB::beginTransaction();

                if (! $this->record->purchasable) {
                    $product = CourseObserver::createProductFromCourse($this->record);
                    $this->record->purchasable = $product;
                }

                if (! count($this->variants)) {
                    $this->record->purchasable
                        ->variants()
                        ->get()
                        ->each(
                            fn (ProductVariantContract $variant) => $variant->delete(),
                        );

                    DB::commit();

                    Notification::make()->title(
                        __('lunarpanel::productoption.widgets.product-options.notifications.save-variants.success.title'),
                    )->success()->send();

                    return;
                }

                foreach ($this->variants as $variantIndex => $variantData) {
                    $variant = new ProductVariant([
                        'product_id' => $this->record->purchasable->id,
                    ]);
                    $basePrice = null;

                    if (! empty($variantData['variant_id'])) {
                        $variant = ProductVariant::find($variantData['variant_id']);
                        $basePrice = $variant->basePrices->first();
                    }

                    if (! empty($variantData['copied_id'])) {
                        $copiedVariant = ProductVariant::find(
                            $variantData['copied_id'],
                        );

                        $variant = $copiedVariant->replicate();
                        $variant->save();

                        $basePrice = $copiedVariant->basePrices->first()->replicate();
                        $basePrice->priceable_id = $variant->id;
                    }

                    $variant->sku = $variantData['sku'];
                    $variant->stock = $variantData['stock'];
                    $variant->tax_class_id = $variantData['tax_class_id'];
                    $variant->save();

                    $basePrice->price = (int) bcmul($variantData['price'], $basePrice->currency->factor);
                    $basePrice->save();

                    $optionsValues = $this->mapOptionValuesToIds($variantData['values']);

                    $variant->values()->sync($optionsValues);

                    $this->variants[$variantIndex]['variant_id'] = $variant->id;
                }

                $variantIds = collect($this->variants)->pluck('variant_id');

                $this->record->purchasable
                    ->variants()->whereNotIn('id', $variantIds)
                    ->get()
                    ->each(
                        fn ($variant) => $variant->delete(),
                    );

                DB::commit();

                Notification::make()->title(
                    __('lunarpanel::productoption.widgets.product-options.notifications.save-variants.success.title'),
                )->success()->send();
            })->after(
                fn () => ProductVariantOptionsUpdated::dispatch($this->record),
            );
    }

    public function getVariantLink($variantId): string
    {
        return ProductVariantResource::getUrl('edit', [
            'product' => $this->record->purchasable->id,
            'record' => $variantId,
        ]);
    }
}