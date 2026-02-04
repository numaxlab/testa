<?php

namespace Testa\Payment;

use InvalidArgumentException;
use Testa\Contracts\Payment\PaymentGatewayAdapter;

class PaymentGatewayRegistry
{
    /** @var array<string, PaymentGatewayAdapter> */
    private array $adapters = [];

    public function register(PaymentGatewayAdapter $adapter): self
    {
        $this->adapters[$adapter->getDriverName()] = $adapter;

        return $this;
    }

    public function getAdapterForPaymentType(string $paymentType): PaymentGatewayAdapter
    {
        $driver = config("lunar.payments.types.{$paymentType}.driver");

        if ($driver === null) {
            throw new InvalidArgumentException("No driver configured for payment type: {$paymentType}");
        }

        return $this->getAdapter($driver);
    }

    public function getAdapter(string $driverName): PaymentGatewayAdapter
    {
        if (! isset($this->adapters[$driverName])) {
            throw new InvalidArgumentException("No adapter registered for driver: {$driverName}");
        }

        return $this->adapters[$driverName];
    }

    public function hasAdapter(string $driverName): bool
    {
        return isset($this->adapters[$driverName]);
    }

    public function getRegisteredDrivers(): array
    {
        return array_keys($this->adapters);
    }
}
