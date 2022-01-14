<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

interface CurrencyInterface
{
    public function getName(): string;

    public function getPrecision(): int;

    public function convertFromBaseCurrency(float $amount): float;

    public function convertToBaseCurrency(float $amount): float;

    public function setExchangeRate(float $rate): CurrencyInterface;
}
