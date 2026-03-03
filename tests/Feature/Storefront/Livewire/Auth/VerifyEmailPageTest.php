<?php

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\TaxClass;
use Testa\Storefront\Livewire\Auth\VerifyEmailPage;

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

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Verify',
        'last_name' => 'User',
        'email' => 'verify@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => null,
    ]);
    $userModel::reguard();

    $this->verifiedUser = $userModel::create([
        'first_name' => 'Verified',
        'last_name' => 'User',
        'email' => 'verified@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
});

describe('sendVerification', function () {
    it('redirects to dashboard when email is already verified', function () {
        $this->actingAs($this->verifiedUser);

        livewire(VerifyEmailPage::class)
            ->call('sendVerification')
            ->assertRedirect(route('dashboard', absolute: false));
    });

    it('sends notification when email is not verified', function () {
        $this->actingAs($this->user);

        livewire(VerifyEmailPage::class)
            ->call('sendVerification')
            ->assertHasNoErrors();

        Notification::assertSentTo(
            $this->user,
            \Illuminate\Auth\Notifications\VerifyEmail::class,
        );
    });
});

describe('logout', function () {
    it('logs out and redirects to /', function () {
        $this->actingAs($this->user);

        livewire(VerifyEmailPage::class)
            ->call('logout')
            ->assertRedirect('/');

        expect(auth()->check())->toBeFalse();
    });
});
