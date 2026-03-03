<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Education\Course;
use Testa\Observers\CourseObserver;
use Testa\Storefront\Livewire\Account\CoursesListPage;

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

    $userModel = config('auth.providers.users.model');
    $userModel::unguard();
    $this->user = $userModel::create([
        'first_name' => 'Course',
        'last_name' => 'User',
        'email' => 'courselist@example.com',
        'password' => bcrypt('password'),
    ]);
    $userModel::reguard();

    $this->customer = \Lunar\Models\Customer::create([
        'first_name' => 'Course',
        'last_name' => 'User',
    ]);
    $this->customer->users()->attach($this->user);

    // CourseObserver requires a ProductOption with the education-rate handle
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);

    $this->actingAs($this->user);
});

describe('render', function () {
    it('renders successfully with no enrolled courses', function () {
        livewire(CoursesListPage::class)
            ->assertOk();
    });

    it('shows enrolled published courses', function () {
        $course = Course::factory()->create(['is_published' => true]);
        $this->customer->courses()->attach($course->id);

        livewire(CoursesListPage::class)
            ->assertOk();
    });

    it('excludes unpublished courses', function () {
        $publishedCourse = Course::factory()->create(['is_published' => true]);
        $unpublishedCourse = Course::factory()->create(['is_published' => false]);

        $this->customer->courses()->attach($publishedCourse->id);
        $this->customer->courses()->attach($unpublishedCourse->id);

        livewire(CoursesListPage::class)
            ->assertOk();
    });
});
