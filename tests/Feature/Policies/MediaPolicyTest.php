<?php

use Illuminate\Support\Facades\Schema;
use Lunar\Models\Currency;
use Lunar\Models\Customer;
use Lunar\Models\Language;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\TaxClass;
use Testa\Models\Attachment;
use Testa\Observers\CourseObserver;
use Testa\Models\Education\Course;
use Testa\Models\Education\CourseModule;
use Testa\Models\Media\Audio;
use Testa\Models\Media\Video;
use Testa\Policies\MediaPolicy;

beforeEach(function () {
    Schema::table('users', function ($table) {
        $table->dropColumn('name');
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
    });

    config(['auth.providers.users.model' => \Testa\Tests\Stubs\User::class]);

    $this->language = Language::factory()->create(['default' => true]);
    $this->currency = Currency::factory()->create(['default' => true, 'decimal_places' => 2]);
    $this->taxClass = TaxClass::factory()->create(['default' => true]);

    // Required by CourseObserver when Course::factory() fires
    $productOption = ProductOption::factory()->create([
        'handle' => CourseObserver::RATE_PRODUCT_OPTION_HANDLE,
    ]);
    ProductOptionValue::factory()->create(['product_option_id' => $productOption->id]);

    $this->policy = new MediaPolicy();
});

describe('MediaPolicy view', function () {
    it('allows anyone to view public media', function () {
        $audio = Audio::factory()->public()->create();

        expect($this->policy->view(null, $audio))->toBeTrue();
    });

    it('denies guest access to private media', function () {
        $audio = Audio::factory()->private()->create();

        expect($this->policy->view(null, $audio))->toBeFalse();
    });

    it('denies authenticated user access to private media without enrollment', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $audio = Audio::factory()->private()->create();
        $course = Course::factory()->create();

        Attachment::create([
            'attachable_type' => (new Course)->getMorphClass(),
            'attachable_id' => $course->id,
            'media_type' => (new Audio)->getMorphClass(),
            'media_id' => $audio->id,
            'position' => 0,
        ]);

        expect($this->policy->view($user, $audio))->toBeFalse();
    });

    it('allows enrolled user to view private media attached to their course', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $course = Course::factory()->create();
        $testaCustomer = \Testa\Models\Customer::find($customer->id);
        $testaCustomer->courses()->attach($course);

        $audio = Audio::factory()->private()->create();

        Attachment::create([
            'attachable_type' => (new Course)->getMorphClass(),
            'attachable_id' => $course->id,
            'media_type' => (new Audio)->getMorphClass(),
            'media_id' => $audio->id,
            'position' => 0,
        ]);

        expect($this->policy->view($user, $audio))->toBeTrue();
    });

    it('allows enrolled user to view private media attached to their course module', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $course = Course::factory()->create();
        $testaCustomer = \Testa\Models\Customer::find($customer->id);
        $testaCustomer->courses()->attach($course);

        $module = CourseModule::factory()->create([
            'course_id' => $course->id,
        ]);

        $video = Video::factory()->private()->create();

        Attachment::create([
            'attachable_type' => (new CourseModule)->getMorphClass(),
            'attachable_id' => $module->id,
            'media_type' => (new Video)->getMorphClass(),
            'media_id' => $video->id,
            'position' => 0,
        ]);

        expect($this->policy->view($user, $video))->toBeTrue();
    });

    it('denies access to private media when user is enrolled in different course', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $enrolledCourse = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $testaCustomer = \Testa\Models\Customer::find($customer->id);
        $testaCustomer->courses()->attach($enrolledCourse);

        $audio = Audio::factory()->private()->create();

        Attachment::create([
            'attachable_type' => (new Course)->getMorphClass(),
            'attachable_id' => $otherCourse->id,
            'media_type' => (new Audio)->getMorphClass(),
            'media_id' => $audio->id,
            'position' => 0,
        ]);

        expect($this->policy->view($user, $audio))->toBeFalse();
    });

    it('allows public media view for authenticated user', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $audio = Audio::factory()->public()->create();

        expect($this->policy->view($user, $audio))->toBeTrue();
    });

    it('denies access to private media with no attachments', function () {
        $userModel = config('auth.providers.users.model');
        $userModel::unguard();
        $user = $userModel::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $userModel::reguard();

        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $audio = Audio::factory()->private()->create();

        expect($this->policy->view($user, $audio))->toBeFalse();
    });
});
