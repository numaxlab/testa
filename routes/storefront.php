<?php

use Illuminate\Support\Facades\Route;
use NumaxLab\Lunar\Geslib\Storefront\Http\Controllers\Auth\VerifyEmailController;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Actions\Logout;
use Trafikrak\Storefront\Livewire\Account\DashboardPage;
use Trafikrak\Storefront\Livewire\Account\HandleAddressPage;
use Trafikrak\Storefront\Livewire\Account\PasswordPage;
use Trafikrak\Storefront\Livewire\Account\ProfilePage;
use Trafikrak\Storefront\Livewire\Auth\ConfirmPasswordPage;
use Trafikrak\Storefront\Livewire\Auth\ForgotPasswordPage;
use Trafikrak\Storefront\Livewire\Auth\LoginPage;
use Trafikrak\Storefront\Livewire\Auth\RegisterPage;
use Trafikrak\Storefront\Livewire\Auth\ResetPasswordPage;
use Trafikrak\Storefront\Livewire\Auth\VerifyEmailPage;
use Trafikrak\Storefront\Livewire\Bookshop\HomePage as BookshopHomePage;
use Trafikrak\Storefront\Livewire\Bookshop\ItinerariesListPage;
use Trafikrak\Storefront\Livewire\Bookshop\ItineraryPage;
use Trafikrak\Storefront\Livewire\Bookshop\ProductPage;
use Trafikrak\Storefront\Livewire\Bookshop\SearchPage;
use Trafikrak\Storefront\Livewire\Bookshop\SectionPage;
use Trafikrak\Storefront\Livewire\Checkout\ShippingAndPaymentPage;
use Trafikrak\Storefront\Livewire\Checkout\SuccessPage;
use Trafikrak\Storefront\Livewire\Checkout\SummaryPage;
use Trafikrak\Storefront\Livewire\Editorial\AuthorPage;
use Trafikrak\Storefront\Livewire\Editorial\AuthorsListPage;
use Trafikrak\Storefront\Livewire\Editorial\CollectionPage;
use Trafikrak\Storefront\Livewire\Editorial\HomePage as EditorialHomePage;
use Trafikrak\Storefront\Livewire\Education\HomePage as EducationHomePage;
use Trafikrak\Storefront\Livewire\HomePage;
use Trafikrak\Storefront\Livewire\Media\HomePage as MediaHomePage;

Route::get('/', HomePage::class)
    ->name('trafikrak.storefront.homepage');

Route::prefix('/libreria')->group(function () {
    Route::get('/', BookshopHomePage::class)
        ->name('trafikrak.storefront.bookshop.homepage');

    Route::get('/secciones/{slug}', SectionPage::class)
        ->name('trafikrak.storefront.bookshop.sections.show');

    Route::get('/itinerarios', ItinerariesListPage::class)
        ->name('trafikrak.storefront.bookshop.itineraries.index');

    Route::get('/itinerarios/{slug}', ItineraryPage::class)
        ->name('trafikrak.storefront.bookshop.itineraries.show');

    Route::get('/productos/{slug}', ProductPage::class)
        ->name('trafikrak.storefront.bookshop.products.show');

    Route::get('/buscar', SearchPage::class)
        ->name('trafikrak.storefront.bookshop.search');
});

Route::prefix('/editorial')->group(function () {
    Route::get('/', EditorialHomePage::class)
        ->name('trafikrak.storefront.editorial.homepage');

    Route::get('/autoras', AuthorsListPage::class)
        ->name('trafikrak.storefront.editorial.authors.index');

    Route::get('/autoras/{slug}', AuthorPage::class)
        ->name('trafikrak.storefront.editorial.authors.show');

    Route::get('/colecciones/{slug}', CollectionPage::class)
        ->name('trafikrak.storefront.editorial.collections.show');
});

Route::prefix('/formacion')->group(function () {
    Route::get('/', EducationHomePage::class)
        ->name('trafikrak.storefront.education.homepage');
});

Route::prefix('/mediateca')->group(function () {
    Route::get('/', MediaHomePage::class)
        ->name('trafikrak.storefront.media.homepage');
});

Route::prefix('/actualidad')->group(function () {
    //
});

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/registrate', RegisterPage::class)->name('register');
    Route::get('/recuperar-contrasenha', ForgotPasswordPage::class)->name('password.request');
    Route::get('/recuperar-ctonrasenha/{token}', ResetPasswordPage::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/verificar-email', VerifyEmailPage::class)->name('verification.notice');

    Route::get('/verificar-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('/confirmar-contrasenha', ConfirmPasswordPage::class)->name('password.confirm');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');

    Route::redirect('/preferencias', '/preferencias/perfil');

    Route::get('/preferencias/perfil', ProfilePage::class)->name('settings.profile');
    Route::get('/preferencias/contrasenha', PasswordPage::class)->name('settings.password');
    Route::get('/preferencias/direcciones', HandleAddressPage::class)->name('settings.add-address');
    Route::get('/preferencias/direcciones/{id}/editar', HandleAddressPage::class)->name('settings.edit-address');
});

Route::post('logout', Logout::class)->name('logout');

Route::prefix('/checkout')->group(function () {
    Route::get('/', SummaryPage::class)
        ->name('trafikrak.storefront.checkout.summary');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/envio-y-pago', ShippingAndPaymentPage::class)
            ->name('trafikrak.storefront.checkout.shipping-and-payment');

        Route::get('/finalizado/{fingerprint}', SuccessPage::class)
            ->name('trafikrak.storefront.checkout.success');
    });
});