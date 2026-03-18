<?php

use Illuminate\Support\Facades\Route;
use NumaxLab\Lunar\Geslib\Storefront\Http\Controllers\Auth\VerifyEmailController;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Actions\Logout;
use Testa\Storefront\Http\Controllers\ProcessPaymentController;
use Testa\Storefront\Livewire\Account\CoursesListPage as AccountCoursesListPage;
use Testa\Storefront\Livewire\Account\DashboardPage;
use Testa\Storefront\Livewire\Account\FavouriteProductsPage;
use Testa\Storefront\Livewire\Account\HandleAddressPage;
use Testa\Storefront\Livewire\Account\OrderPage;
use Testa\Storefront\Livewire\Account\OrdersListPage;
use Testa\Storefront\Livewire\Account\PasswordPage;
use Testa\Storefront\Livewire\Account\ProfilePage;
use Testa\Storefront\Livewire\Auth\ConfirmPasswordPage;
use Testa\Storefront\Livewire\Auth\ForgotPasswordPage;
use Testa\Storefront\Livewire\Auth\LoginPage;
use Testa\Storefront\Livewire\Auth\RegisterPage;
use Testa\Storefront\Livewire\Auth\ResetPasswordPage;
use Testa\Storefront\Livewire\Auth\VerifyEmailPage;
use Testa\Storefront\Livewire\Bookshop\HomePage as BookshopHomePage;
use Testa\Storefront\Livewire\Bookshop\ItinerariesListPage;
use Testa\Storefront\Livewire\Bookshop\ItineraryPage;
use Testa\Storefront\Livewire\Bookshop\ProductPage;
use Testa\Storefront\Livewire\Bookshop\SearchPage as BookshopSearchPage;
use Testa\Storefront\Livewire\Bookshop\SectionPage;
use Testa\Storefront\Livewire\Bookshop\TopicPage as BookshopTopicPage;
use Testa\Storefront\Livewire\Checkout\ShippingAndPaymentPage;
use Testa\Storefront\Livewire\Checkout\SuccessPage;
use Testa\Storefront\Livewire\Checkout\SummaryPage;
use Testa\Storefront\Livewire\Editorial\AuthorPage;
use Testa\Storefront\Livewire\Editorial\AuthorsListPage;
use Testa\Storefront\Livewire\Editorial\CollectionPage;
use Testa\Storefront\Livewire\Editorial\HomePage as EditorialHomePage;
use Testa\Storefront\Livewire\Editorial\SpecialCollectionPage;
use Testa\Storefront\Livewire\Education\CoursePage;
use Testa\Storefront\Livewire\Education\CourseRegisterPage;
use Testa\Storefront\Livewire\Education\CourseRegisterSuccessPage;
use Testa\Storefront\Livewire\Education\CoursesListPage;
use Testa\Storefront\Livewire\Education\HomePage as EducationHomePage;
use Testa\Storefront\Livewire\Education\ModulePage;
use Testa\Storefront\Livewire\Education\TopicPage as EducationTopicPage;
use Testa\Storefront\Livewire\Education\TopicsListPage;
use Testa\Storefront\Livewire\HomePage;
use Testa\Storefront\Livewire\KitchenSinkPage;
use Testa\Storefront\Livewire\Media\AudioPage;
use Testa\Storefront\Livewire\Media\DocumentsListPage;
use Testa\Storefront\Livewire\Media\HomePage as MediaHomePage;
use Testa\Storefront\Livewire\Media\SearchPage as MediaSearchPage;
use Testa\Storefront\Livewire\Media\VideoPage;
use Testa\Storefront\Livewire\Membership\DonatePage;
use Testa\Storefront\Livewire\Membership\DonateSuccessPage;
use Testa\Storefront\Livewire\Membership\SignupPage;
use Testa\Storefront\Livewire\Membership\SignupSuccessPage;
use Testa\Storefront\Livewire\News\ActivitiesCalendarPage;
use Testa\Storefront\Livewire\News\ActivitiesListPage;
use Testa\Storefront\Livewire\News\ArticlePage;
use Testa\Storefront\Livewire\News\ArticlesListPage;
use Testa\Storefront\Livewire\News\EventPage;
use Testa\Storefront\Livewire\News\HomePage as NewsHomePage;
use Testa\Storefront\Livewire\PagePage;

Route::get('/', HomePage::class)
    ->name('testa.storefront.homepage');

Route::get('/persona/{slug}', \Testa\Storefront\Livewire\AuthorPage::class)
    ->name('testa.storefront.authors.show');

Route::prefix('/libreria')->group(function () {
    Route::get('/', BookshopHomePage::class)
        ->name('testa.storefront.bookshop.homepage');

    Route::get('/secciones/{slug}', SectionPage::class)
        ->name('testa.storefront.bookshop.sections.show');

    Route::get('/materias/{slug}', BookshopTopicPage::class)
        ->name('testa.storefront.bookshop.topics.show');

    Route::get('/itinerarios', ItinerariesListPage::class)
        ->name('testa.storefront.bookshop.itineraries.index');

    Route::get('/itinerarios/{slug}', ItineraryPage::class)
        ->name('testa.storefront.bookshop.itineraries.show');

    Route::get('/productos/{slug}', ProductPage::class)
        ->name('testa.storefront.bookshop.products.show');

    Route::get('/buscar', BookshopSearchPage::class)
        ->name('testa.storefront.bookshop.search');

    Route::get('/{slug}', PagePage::class)
        ->name('testa.storefront.bookshop.page');
});

Route::prefix('/editorial')->group(function () {
    Route::get('/', EditorialHomePage::class)
        ->name('testa.storefront.editorial.homepage');

    Route::get('/autoras', AuthorsListPage::class)
        ->name('testa.storefront.editorial.authors.index');

    Route::get('/autoras/{slug}', AuthorPage::class)
        ->name('testa.storefront.editorial.authors.show');

    Route::get('/colecciones/{slug}', CollectionPage::class)
        ->name('testa.storefront.editorial.collections.show');

    Route::get('/especiales/{slug}', SpecialCollectionPage::class)
        ->name('testa.storefront.editorial.collections.special.show');

    Route::get('/{slug}', PagePage::class)
        ->name('testa.storefront.editorial.page');
});

Route::prefix('/formacion')->group(function () {
    Route::get('/', EducationHomePage::class)
        ->name('testa.storefront.education.homepage');

    Route::get('/temas', TopicsListPage::class)
        ->name('testa.storefront.education.topics.index');

    Route::get('/temas/{slug}', EducationTopicPage::class)
        ->name('testa.storefront.education.topics.show');

    Route::get('/cursos', CoursesListPage::class)
        ->name('testa.storefront.education.courses.index');

    Route::get('/cursos/{slug}', CoursePage::class)
        ->name('testa.storefront.education.courses.show');

    Route::get('/cursos/{slug}/inscripcion', CourseRegisterPage::class)
        ->name('testa.storefront.education.courses.register');

    Route::get('/cursos/inscripcion/finalizada/{id}/{fingerprint}', CourseRegisterSuccessPage::class)
        ->name('testa.storefront.education.courses.register.success');

    Route::get('/cursos/{courseSlug}/sesiones/{moduleSlug}', ModulePage::class)
        ->name('testa.storefront.education.courses.modules.show');

    Route::get('/{slug}', PagePage::class)
        ->name('testa.storefront.education.page');
});

Route::prefix('/mediateca')->group(function () {
    Route::get('/', MediaHomePage::class)
        ->name('testa.storefront.media.homepage');

    Route::get('/audiovisual/buscar', MediaSearchPage::class)
        ->name('testa.storefront.media.search');

    Route::get('/videos/{slug}', VideoPage::class)
        ->name('testa.storefront.media.videos.show');

    Route::get('/audios/{slug}', AudioPage::class)
        ->name('testa.storefront.media.audios.show');

    Route::get('/documentos', DocumentsListPage::class)
        ->name('testa.storefront.media.documents.index');
});

Route::prefix('/actualidad')->group(function () {
    Route::get('/', NewsHomePage::class)
        ->name('testa.storefront.news.homepage');

    Route::get('/actividades', ActivitiesListPage::class)
        ->name('testa.storefront.activities.index');

    Route::get('/actividades/calendario', ActivitiesCalendarPage::class)
        ->name('testa.storefront.activities.calendar');

    Route::get('/actividades/eventos/{slug}', EventPage::class)
        ->name('testa.storefront.events.show');

    Route::get('/noticias', ArticlesListPage::class)
        ->name('testa.storefront.articles.index');

    Route::get('/noticias/{slug}', ArticlePage::class)
        ->name('testa.storefront.articles.show');
});

Route::prefix('/info')->group(function () {
    Route::get('/{slug}', PagePage::class)
        ->name('testa.storefront.info.page');
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

    Route::get('/mis-cursos', AccountCoursesListPage::class)
        ->name('my-courses.index');
});

Route::post('logout', Logout::class)->name('logout');

Route::prefix('/pedido')->group(function () {
    Route::get('/', SummaryPage::class)
        ->name('testa.storefront.checkout.summary');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/completar', ShippingAndPaymentPage::class)
            ->name('testa.storefront.checkout.shipping-and-payment');

        Route::get('/finalizado/{id}/{fingerprint}', SuccessPage::class)
            ->name('testa.storefront.checkout.success');
    });
});

Route::prefix('/apoya-el-proyecto')->group(function () {
    Route::get('/hazte-socix', SignupPage::class)
        ->name('testa.storefront.membership.signup');

    Route::get('/hazte-socix/finalizado/{id}/{fingerprint}', SignupSuccessPage::class)
        ->name('testa.storefront.membership.signup.success');

    Route::get('/dona', DonatePage::class)
        ->name('testa.storefront.membership.donate');

    Route::get('/dona/gracias/{id}/{fingerprint}', DonateSuccessPage::class)
        ->name('testa.storefront.membership.donate.success');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout/procesar-pago/{id}', ProcessPaymentController::class)
        ->name('testa.storefront.checkout.process-payment');
});

if (app()->environment('local')) {
    Route::get('/kitchen-sink', KitchenSinkPage::class)
        ->name('testa.storefront.kitchen-sink');
}
