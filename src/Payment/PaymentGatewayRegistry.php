<?php

namespace Testa\Payment;

use InvalidArgumentException;
use Testa\Contracts\Payment\PaymentGatewayAdapter;

class PaymentGatewayRegistry
{
    /** @var array<string, PaymentGatewayAdapter> */
    private array $adapters = [];

    /**
     * Register an adapter for a specific driver name.
     */
    public function register(PaymentGatewayAdapter $adapter): self
    {
        $this->adapters[$adapter->getDriverName()] = $adapter;

        return $this;
    }

    /**
     * Get the adapter for a payment type by looking up its configured driver.
     *
     * @throws InvalidArgumentException
     */
    public function getAdapterForPaymentType(string $paymentType): PaymentGatewayAdapter
    {
        $driver = config("lunar.payments.types.{$paymentType}.driver");

        if ($driver === null) {
            throw new InvalidArgumentException("No driver configured for payment type: {$paymentType}");
        }

        return $this->getAdapter($driver);
    }

    /**
     * Get an adapter by driver name.
     *
     * @throws InvalidArgumentException
     */
    public function getAdapter(string $driverName): PaymentGatewayAdapter
    {
        if (! isset($this->adapters[$driverName])) {
            throw new InvalidArgumentException("No adapter registered for driver: {$driverName}");
        }

        return $this->adapters[$driverName];
    }

    /**
     * Check if an adapter is registered for the given driver name.
     */
    public function hasAdapter(string $driverName): bool
    {
        return isset($this->adapters[$driverName]);
    }

    /**
     * Get all registered adapter driver names.
     *
     * @return array<string>
     */
    public function getRegisteredDrivers(): array
    {
        return array_keys($this->adapters);
    }
}
