<?php

use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRate;
use Lunar\Models\TaxRateAmount;
use Lunar\Models\TaxZone;
use Lunar\Models\TaxZoneCountry;
use Testa\Models\Media\Document;
use Testa\Models\Media\Visibility;
use Testa\Storefront\Livewire\Components\Media\LatestDocuments;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    $this->country = Country::factory()->create();
    $this->taxZone = TaxZone::factory()->create(['default' => true, 'zone_type' => 'country']);
    TaxZoneCountry::factory()->create([
        'tax_zone_id' => $this->taxZone->id,
        'country_id' => $this->country->id,
    ]);
    $this->taxRate = TaxRate::factory()->create(['tax_zone_id' => $this->taxZone->id]);
    TaxRateAmount::factory()->create([
        'tax_rate_id' => $this->taxRate->id,
        'tax_class_id' => $this->taxClass->id,
        'percentage' => 21,
    ]);
});

describe('LatestDocuments component', function () {
    it('renders public published documents as article cards', function () {
        Document::factory()->count(2)->create([
            'visibility' => Visibility::PUBLIC->value,
            'is_published' => true,
        ]);

        livewire(LatestDocuments::class)
            ->assertSeeHtml('<article')
            ->assertOk();
    });

    it('does not render any articles when only members_only documents exist', function () {
        Document::factory()->count(2)->create([
            'visibility' => Visibility::MEMBERS_ONLY->value,
            'is_published' => true,
        ]);

        livewire(LatestDocuments::class)
            ->assertDontSeeHtml('<article')
            ->assertOk();
    });

    it('does not render any articles when only unpublished public documents exist', function () {
        Document::factory()->count(2)->create([
            'visibility' => Visibility::PUBLIC->value,
            'is_published' => false,
        ]);

        livewire(LatestDocuments::class)
            ->assertDontSeeHtml('<article')
            ->assertOk();
    });

    it('limits rendered article cards to 6 even when more exist', function () {
        Document::factory()->count(8)->create([
            'visibility' => Visibility::PUBLIC->value,
            'is_published' => true,
        ]);

        $html = livewire(LatestDocuments::class)->html();

        $count = substr_count($html, '<article');

        expect($count)->toBe(6);
    });
});
