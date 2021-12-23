<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

interface CurrencyServiceInterface
{
    public function format(CurrencyInterface $currency, float $amount): string;

    public function getPrecision(string $amount): int;

    public function findByName(string $name): ?CurrencyInterface;

    public function findOrAddNew(string $name, int $precision): CurrencyInterface;
}
