<?php

namespace Testa\Storefront\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use NumaxLab\Lunar\Geslib\Storefront\Livewire\Page;
use Testa\Livewire\Features\WithPagination;
use Testa\Storefront\Queries\Education\GetCustomerCourses;

class CoursesListPage extends Page
{
    use WithPagination;

    public function render(): View
    {
        $courses = new GetCustomerCourses()->execute(Auth::user()->latestCustomer());

        return view('testa::storefront.livewire.account.courses-list', compact('courses'));
    }
}
