<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

use DY\CFC\Currency\Exception\UnsupportedCurrencyPrecisionException;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use DY\CFC\Service\ExchangeRateLoader;
use DY\CFC\Service\ExchangeRateLoaderInterface;

class CurrencyService implements CurrencyServiceInterface
{
    private const EUR = 'EUR';

    private array $currencies = [];

    public function __construct(private ExchangeRateLoaderInterface $exchangeRateLoader)
    {
    }

    /**
     * @throws ExchangeRatesLoadingException
     */
    public static function createMock(): CurrencyServiceInterface
    {
        return new self(ExchangeRateLoader::createMock());
    }

    /**
     * @throws UnsupportedCurrencyPrecisionException
     */
    public function format(CurrencyInterface $currency, float $amount): string
    {
        if ($currency->getPrecision() === 0) {
            return sprintf("%d", $amount);
        }

        if ($currency->getPrecision() === 2) {
            return sprintf("%.2f", $amount);
        }

        throw new UnsupportedCurrencyPrecisionException();
    }

    public function getPrecision(string $amount): int
    {
        $amountParts = preg_split("/\./", $amount, 2);
        return strlen($amountParts[1] ?? "");
    }

    public function findByName(string $name): ?CurrencyInterface
    {
        return $this->currencies[strtoupper($name)] ?? null;
    }

    public function getExchangeRateFromEuro(string $symbol): float
    {
        return $this->exchangeRateLoader?->getExchangeRate(self::EUR, $symbol) ?? 1;
    }

    public function findOrAddNew(string $name, int $precision): CurrencyInterface
    {
        $uppercaseName = strtoupper($name);

        if (!isset($this->currencies[$uppercaseName])) {
            $currency = new Currency($uppercaseName, $precision);
            $exchangeRate = $this->getExchangeRateFromEuro($currency->getName());
            $currency->setExchangeRateFromEuro($exchangeRate);
            $this->currencies[$uppercaseName] = $currency;
        }

        return $this->currencies[$uppercaseName];
    }
}
