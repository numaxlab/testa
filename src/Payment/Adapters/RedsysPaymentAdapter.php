<?php

namespace Testa\Payment\Adapters;

use NumaxLab\Lunar\Redsys\Responses\RedirectToPaymentGateway;
use Testa\Contracts\Payment\PaymentContext;
use Testa\Contracts\Payment\PaymentGatewayAdapter;
use Testa\Contracts\Payment\PaymentResult;

class RedsysPaymentAdapter implements PaymentGatewayAdapter
{
    public const DRIVER_NAME = 'redsys';

    public function __construct(
        private readonly string $configKey = 'default',
    ) {}

    public function getDriverName(): string
    {
        return self::DRIVER_NAME;
    }

    public function prepareAuthorizationData(PaymentContext $context): array
    {
        return [
            'config_key' => $this->configKey,
            'url_ok' => $context->successRoute,
            'url_ko' => $context->failureRoute,
            'method' => $this->resolvePaymentMethod($context->paymentType),
            'product_description' => 'Compra online en '.config('app.name'),
        ];
    }

    /**
     * Resolve the Redsys payment method code based on payment type.
     * - 'z' = Bizum
     * - 'C' = Card (default)
     */
    private function resolvePaymentMethod(string $paymentType): string
    {
        return match ($paymentType) {
            'bizum' => 'z',
            default => 'C',
        };
    }

    public function handleAuthorizationResponse(
        mixed $response,
        object $paymentDriver,
        PaymentContext $context,
    ): PaymentResult {
        if (! $response->success) {
            return PaymentResult::failure('Payment authorization failed');
        }

        if ($response instanceof RedirectToPaymentGateway) {
            return PaymentResult::redirect($paymentDriver);
        }

        return PaymentResult::success($response->orderId);
    }
}
