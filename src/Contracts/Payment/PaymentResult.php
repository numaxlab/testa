<?php

namespace Testa\Contracts\Payment;

final readonly class PaymentResult
{
    public function __construct(
        public PaymentResultType $type,
        public ?int $orderId = null,
        public ?string $redirectUrl = null,
        public ?string $errorMessage = null,
        public mixed $paymentDriver = null,
    ) {}

    public static function success(int $orderId): self
    {
        return new self(
            type: PaymentResultType::Success,
            orderId: $orderId,
        );
    }

    public static function redirect(mixed $paymentDriver): self
    {
        return new self(
            type: PaymentResultType::Redirect,
            paymentDriver: $paymentDriver,
        );
    }

    public static function failure(?string $errorMessage = null): self
    {
        return new self(
            type: PaymentResultType::Failure,
            errorMessage: $errorMessage,
        );
    }

    public static function pending(int $orderId): self
    {
        return new self(
            type: PaymentResultType::Pending,
            orderId: $orderId,
        );
    }

    public function isSuccess(): bool
    {
        return $this->type === PaymentResultType::Success;
    }

    public function isRedirect(): bool
    {
        return $this->type === PaymentResultType::Redirect;
    }

    public function isFailure(): bool
    {
        return $this->type === PaymentResultType::Failure;
    }

    public function isPending(): bool
    {
        return $this->type === PaymentResultType::Pending;
    }
}
