<?php

namespace Testa\Storefront\Livewire\Components\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Testa\Storefront\Queries\Education\GetCustomerLatestCourse;

class LatestCourse extends Component
{
    public function render(): View
    {
        $course = new GetCustomerLatestCourse()->execute(Auth::user()->latestCustomer());

        return view('testa::storefront.livewire.components.account.latest-course', compact('course'));
    }
}
