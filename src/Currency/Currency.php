<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

class Currency implements CurrencyInterface
{
    public function __construct(
        private string $name,
        private int $precision,
        private float $exchangeRate = 1
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

    public function setExchangeRate(float $rate): CurrencyInterface
    {
        $this->exchangeRate = $rate;
        return $this;
    }

    public function convertFromBaseCurrency(float $amount): float
    {
        return $this->exchangeRate * $amount;
    }

    public function convertToBaseCurrency(float $amount): float
    {
        return $amount / $this->exchangeRate;
    }
}
