<?php

use Illuminate\Support\Facades\Route;
use NumaxLab\Lunar\Geslib\Storefront\Http\Controllers\Auth\VerifyEmailController;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Actions\Logout;
use Trafikrak\Storefront\Http\Controllers\ProcessPaymentController;
use Trafikrak\Storefront\Livewire\Account\DashboardPage;
use Trafikrak\Storefront\Livewire\Account\FavouriteProductsPage;
use Trafikrak\Storefront\Livewire\Account\HandleAddressPage;
use Trafikrak\Storefront\Livewire\Account\OrderPage;
use Trafikrak\Storefront\Livewire\Account\OrdersListPage;
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
use Trafikrak\Storefront\Livewire\Bookshop\SearchPage as BookshopSearchPage;
use Trafikrak\Storefront\Livewire\Bookshop\SectionPage;
use Trafikrak\Storefront\Livewire\Checkout\ShippingAndPaymentPage;
use Trafikrak\Storefront\Livewire\Checkout\SuccessPage;
use Trafikrak\Storefront\Livewire\Checkout\SummaryPage;
use Trafikrak\Storefront\Livewire\Editorial\AuthorPage;
use Trafikrak\Storefront\Livewire\Editorial\AuthorsListPage;
use Trafikrak\Storefront\Livewire\Editorial\CollectionPage;
use Trafikrak\Storefront\Livewire\Editorial\HomePage as EditorialHomePage;
use Trafikrak\Storefront\Livewire\Editorial\SpecialCollectionPage;
use Trafikrak\Storefront\Livewire\Education\CoursePage;
use Trafikrak\Storefront\Livewire\Education\CourseRegisterPage;
use Trafikrak\Storefront\Livewire\Education\CoursesListPage;
use Trafikrak\Storefront\Livewire\Education\HomePage as EducationHomePage;
use Trafikrak\Storefront\Livewire\Education\ModulePage;
use Trafikrak\Storefront\Livewire\Education\TopicPage;
use Trafikrak\Storefront\Livewire\Education\TopicsListPage;
use Trafikrak\Storefront\Livewire\HomePage;
use Trafikrak\Storefront\Livewire\KitchenSinkPage;
use Trafikrak\Storefront\Livewire\Media\DocumentsListPage;
use Trafikrak\Storefront\Livewire\Media\HomePage as MediaHomePage;
use Trafikrak\Storefront\Livewire\Media\SearchPage as MediaSearchPage;
use Trafikrak\Storefront\Livewire\Media\VideosListPage;
use Trafikrak\Storefront\Livewire\Membership\DonatePage;
use Trafikrak\Storefront\Livewire\Membership\DonateSuccessPage;
use Trafikrak\Storefront\Livewire\Membership\SignupPage;
use Trafikrak\Storefront\Livewire\Membership\SignupSuccessPage;
use Trafikrak\Storefront\Livewire\PagePage;

Route::get('/', HomePage::class)
    ->name('trafikrak.storefront.homepage');

Route::get('/persona/{slug}', \Trafikrak\Storefront\Livewire\AuthorPage::class)
    ->name('trafikrak.storefront.authors.show');

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

    Route::get('/buscar', BookshopSearchPage::class)
        ->name('trafikrak.storefront.bookshop.search');

    Route::get('/{slug}', PagePage::class)
        ->name('trafikrak.storefront.bookshop.page');
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

    Route::get('/especiales/{slug}', SpecialCollectionPage::class)
        ->name('trafikrak.storefront.editorial.collections.special.show');

    Route::get('/{slug}', PagePage::class)
        ->name('trafikrak.storefront.editorial.page');
});

Route::prefix('/formacion')->group(function () {
    Route::get('/', EducationHomePage::class)
        ->name('trafikrak.storefront.education.homepage');

    Route::get('/temas', TopicsListPage::class)
        ->name('trafikrak.storefront.education.topics.index');

    Route::get('/temas/{slug}', TopicPage::class)
        ->name('trafikrak.storefront.education.topics.show');

    Route::get('/cursos', CoursesListPage::class)
        ->name('trafikrak.storefront.education.courses.index');

    Route::get('/cursos/{slug}', CoursePage::class)
        ->name('trafikrak.storefront.education.courses.show');

    Route::get('/cursos/{slug}/inscripcion', CourseRegisterPage::class)
        ->name('trafikrak.storefront.education.courses.register');

    Route::get('/cursos/{courseSlug}/sesiones/{moduleSlug}', ModulePage::class)
        ->name('trafikrak.storefront.education.courses.modules.show');

    Route::get('/{slug}', PagePage::class)
        ->name('trafikrak.storefront.education.page');
});

Route::prefix('/mediateca')->group(function () {
    Route::get('/', MediaHomePage::class)
        ->name('trafikrak.storefront.media.homepage');

    Route::get('/audiovisual/buscar', MediaSearchPage::class)
        ->name('trafikrak.storefront.media.search');

    Route::get('/documentos', DocumentsListPage::class)
        ->name('trafikrak.storefront.media.documents.index');
});

Route::prefix('/actualidad')->group(function () {
    //
});

Route::prefix('/info')->group(function () {
    Route::get('/{slug}', PagePage::class)
        ->name('trafikrak.storefront.info.page');
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
    Route::get('/mi-cuenta', DashboardPage::class)->name('dashboard');

    Route::redirect('/preferencias', '/preferencias/perfil');

    Route::get('/preferencias/perfil', ProfilePage::class)->name('settings.profile');
    Route::get('/preferencias/contrasenha', PasswordPage::class)->name('settings.password');
    Route::get('/preferencias/direcciones', HandleAddressPage::class)->name('settings.add-address');
    Route::get('/preferencias/direcciones/{id}/editar', HandleAddressPage::class)->name('settings.edit-address');

    Route::get('/mis-favoritos', FavouriteProductsPage::class)->name('favourite-products.index');

    Route::get('/mis-pedidos', OrdersListPage::class)->name('orders.index');
    Route::get('/mis-pedidos/{reference}', OrderPage::class)->name('orders.show');
});

Route::post('logout', Logout::class)->name('logout');

Route::prefix('/pedido')->group(function () {
    Route::get('/', SummaryPage::class)
        ->name('trafikrak.storefront.checkout.summary');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/completar', ShippingAndPaymentPage::class)
            ->name('trafikrak.storefront.checkout.shipping-and-payment');

        Route::get('/finalizado/{fingerprint}', SuccessPage::class)
            ->name('trafikrak.storefront.checkout.success');
    });
});

Route::prefix('/apoya-el-proyecto')->group(function () {
    Route::get('/hazte-socix', SignupPage::class)
        ->name('trafikrak.storefront.membership.signup');

    Route::get('/hazte-socix/finalizado/{fingerprint}', SignupSuccessPage::class)
        ->name('trafikrak.storefront.membership.signup.success');

    Route::get('/dona', DonatePage::class)
        ->name('trafikrak.storefront.membership.donate');

    Route::get('/dona/gracias/{fingerprint}', DonateSuccessPage::class)
        ->name('trafikrak.storefront.membership.donate.success');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout/procesar-pago/{id}', ProcessPaymentController::class)
        ->name('trafikrak.storefront.checkout.process-payment');
});

if (app()->environment('local')) {
    Route::get('/kitchen-sink', KitchenSinkPage::class)
        ->name('trafikrak.storefront.kitchen-sink');
}
