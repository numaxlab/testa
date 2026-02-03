<?php

namespace Testa\Payment\Adapters;

use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentGatewayAdapter;
use Testa\Contracts\Payment\PaymentResult;

class OfflinePaymentAdapter implements PaymentGatewayAdapter
{
    public const DRIVER_NAME = 'offline';

    public function getDriverName(): string
    {
        return self::DRIVER_NAME;
    }

    public function prepareAuthorizationData(PaymentContext $context): array
    {
        return [];
    }

    public function handleAuthorizationResponse(
        mixed $response,
        object $paymentDriver,
        PaymentContext $context,
    ): PaymentResult {
        if (! $response->success) {
            return PaymentResult::failure('Payment authorization failed');
        }

        return PaymentResult::success($response->orderId);
    }
}
