<?php

namespace Testa\Payment;

final readonly class RecurringChargeResult
{
    public function __construct(
        public bool $success,
        public bool $aborted = false,
        public ?string $errorMessage = null,
        public ?string $redsysResponse = null,
    ) {}

    public static function success(): self
    {
        return new self(success: true);
    }

    public static function failure(string $errorMessage, ?string $redsysResponse = null): self
    {
        return new self(
            success: false,
            aborted: false,
            errorMessage: $errorMessage,
            redsysResponse: $redsysResponse,
        );
    }

    public static function aborted(string $reason): self
    {
        return new self(
            success: false,
            aborted: true,
            errorMessage: $reason,
        );
    }
}
