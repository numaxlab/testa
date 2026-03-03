<?php

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\ForgotPasswordPage;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->channel = Channel::factory()->create(['default' => true]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);
    $this->customerGroup = CustomerGroup::factory()->create(['default' => true]);

    Notification::fake();
});

describe('validation', function () {
    it('validates email is required', function () {
        livewire(ForgotPasswordPage::class)
            ->set('email', '')
            ->call('sendPasswordResetLink')
            ->assertHasErrors(['email']);
    });

    it('validates email must be a valid email address', function () {
        livewire(ForgotPasswordPage::class)
            ->set('email', 'not-an-email')
            ->call('sendPasswordResetLink')
            ->assertHasErrors(['email']);
    });
});

describe('sendPasswordResetLink', function () {
    it('completes without errors when given a valid email', function () {
        livewire(ForgotPasswordPage::class)
            ->set('email', 'any@example.com')
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors();
    });

    it('completes without errors for a non-existent user email', function () {
        livewire(ForgotPasswordPage::class)
            ->set('email', 'nonexistent@example.com')
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors();
    });
});
