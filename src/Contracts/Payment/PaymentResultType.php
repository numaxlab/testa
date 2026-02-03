<?php

namespace Testa\Contracts\Payment;

enum PaymentResultType: string
{
    case Success = 'success';
    case Redirect = 'redirect';
    case Failure = 'failure';
    case Pending = 'pending';
}
