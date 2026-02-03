<?php

namespace Testa\Contracts\Payment;

interface PaymentGatewayAdapter
{
    /**
     * Get the driver name this adapter handles.
     * This should match the driver configured in lunar.payments.types.{type}.driver
     */
    public function getDriverName(): string;

    /**
     * Prepare the data array to be passed to the payment driver's authorize method.
     */
    public function prepareAuthorizationData(PaymentContext $context): array;

    /**
     * Handle the response from the payment driver's authorize method.
     */
    public function handleAuthorizationResponse(
        mixed $response,
        object $paymentDriver,
        PaymentContext $context,
    ): PaymentResult;
}
