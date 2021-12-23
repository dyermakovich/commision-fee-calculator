<?php

declare(strict_types=1);

namespace DY\CFC\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface ExchangeRateLoaderInterface
{
    public function setHttpClient(HttpClientInterface $client): ExchangeRateLoaderInterface;

    public function loadExchangeRates(string $baseCurrency, array $symbols): ExchangeRateLoaderInterface;

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float;
}
