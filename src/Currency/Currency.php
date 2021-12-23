<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

class Currency implements CurrencyInterface
{
    public function __construct(
        private string $name,
        private int $precision,
        private float $exchangeRageFromEuro = 1
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function setExchangeRateFromEuro(float $rate): CurrencyInterface
    {
        $this->exchangeRageFromEuro = $rate;
        return $this;
    }

    public function convertFromEuro(float $amountInEuro): float
    {
        return $this->exchangeRageFromEuro * $amountInEuro;
    }

    public function convertToEuro(float $amount): float
    {
        return $amount / $this->exchangeRageFromEuro;
    }
}
