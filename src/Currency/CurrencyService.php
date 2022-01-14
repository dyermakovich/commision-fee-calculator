<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

use DY\CFC\Config\Config;
use DY\CFC\Config\ConfigInterface;
use DY\CFC\Currency\Exception\UnsupportedCurrencyPrecisionException;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use DY\CFC\Service\ExchangeRateLoader;
use DY\CFC\Service\ExchangeRateLoaderInterface;

class CurrencyService implements CurrencyServiceInterface
{
    private array $currencies = [];

    public function __construct(
        private ExchangeRateLoaderInterface $exchangeRateLoader,
        private ConfigInterface $config
    ) {
    }

    /**
     * @throws ExchangeRatesLoadingException
     */
    public static function createMock(): CurrencyServiceInterface
    {
        return new self(ExchangeRateLoader::createMock(), Config::create());
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

    public function getExchangeRate(string $symbol): float
    {
        return $this->exchangeRateLoader?->getExchangeRate(
            $this->config->getBaseCurrency(),
            $symbol
        ) ?? 1;
    }

    public function findOrAddNew(string $name, int $precision): CurrencyInterface
    {
        $uppercaseName = strtoupper($name);

        if (!isset($this->currencies[$uppercaseName])) {
            $currency = new Currency($uppercaseName, $precision);
            $exchangeRate = $this->getExchangeRate($currency->getName());
            $currency->setExchangeRate($exchangeRate);
            $this->currencies[$uppercaseName] = $currency;
        }

        return $this->currencies[$uppercaseName];
    }
}
