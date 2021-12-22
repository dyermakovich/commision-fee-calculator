<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

class Currency implements CurrencyInterface
{
    private CurrencyServiceInterface $currencyService;

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

    public function setExchangeRateFromEuro(float $rate): void
    {
        $this->exchangeRageFromEuro = $rate;
    }

    public function convertFromEuro(float $amountInEuro): float
    {
        return $this->exchangeRageFromEuro * $amountInEuro;
    }
}
