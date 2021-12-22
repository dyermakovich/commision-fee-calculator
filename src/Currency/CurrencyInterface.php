<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

interface CurrencyInterface
{
    public function getName(): string;

    public function getPrecision(): int;

    public function convertFromEuro(float $amountInEuro): float;

    public function setExchangeRateFromEuro(float $rate): void;
}
