<?php

declare(strict_types=1);

namespace DY\CFC\Service;

use DY\CFC\Service\Exception\ExchangeRateNotFoundException;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateLoader implements ExchangeRateLoaderInterface
{
    private const URL = 'http://api.exchangeratesapi.io/latest?access_key=%s&base=%s&symbols=%s';

    private ?HttpClientInterface $client;
    private array $rates;

    /**
     * @throws ExchangeRatesLoadingException
     */
    public static function create(?HttpClientInterface $client = null): ExchangeRateLoaderInterface
    {
        $loader = new ExchangeRateLoader();

        if (isset($client)) {
            $loader->setHttpClient($client);
        }

        return $loader->loadExchangeRates("EUR", ["USD", "JPY"]);
    }

    public function setHttpClient(HttpClientInterface $client): ExchangeRateLoaderInterface
    {
        $this->client = $client;
        return $this;
    }

    private function getClient(): HttpClientInterface
    {
        if (!isset($this->client)) {
            $this->client = HttpClient::create();
        }
        return $this->client;
    }

    /**
     * @throws ExchangeRatesLoadingException
     */
    public function loadExchangeRates(string $baseCurrency, array $symbols): ExchangeRateLoaderInterface
    {
        $ratesLoaded = $this->getRates($baseCurrency, $symbols);

        if (!isset($this->rates[$baseCurrency])) {
            $this->rates[$baseCurrency] = [];
        }

        $this->rates[$baseCurrency] = array_merge($this->rates[$baseCurrency], $ratesLoaded);
        return $this;
    }

    /**
     * @throws ExchangeRatesLoadingException
     */
    private function getRates(string $baseCurrency, array $symbols): array
    {
        $apiKey = defined("EXCHANGE_RATES_API_KEY") ? EXCHANGE_RATES_API_KEY : "";
        $currencySymbols = implode(",", $symbols);

        try {
            $response = $this->getClient()->request(
                "GET",
                sprintf(self::URL, $apiKey, $baseCurrency, $currencySymbols)
            );

            $content = json_decode($response->getContent());
        } catch (TransportExceptionInterface
            | ClientExceptionInterface
            | RedirectionExceptionInterface
            | ServerExceptionInterface $e
        ) {
            throw new ExchangeRatesLoadingException('Can\'t load exchange rates from API server due to error: "%s".', $e);
        }

        if (!isset($content->success) || !$content->success) {
            throw new ExchangeRatesLoadingException(
                sprintf(
                    'API server has returned an error: "%s".',
                    $content->error->info
                )
            );
        }

        $result = array();

        foreach (get_object_vars($content->rates) as $symbol => $rate) {
            $result[$symbol] = $rate;
        }

        return $result;
    }

    /**
     * @throws ExchangeRateNotFoundException
     */
    private function getExchangeRatesFromCurrency(string $fromCurrency): array
    {
        if (!isset($this->rates[$fromCurrency])) {
            throw new ExchangeRateNotFoundException();
        }
        return $this->rates[$fromCurrency];
    }

    /**
     * @throws ExchangeRateNotFoundException
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $fromCurrencyUpperCase = strtoupper($fromCurrency);
        $toCurrencyUpperCase = strtoupper($toCurrency);

        if ($fromCurrency === $toCurrency) {
            return 1;
        }

        $exchangeRatesForCurrency = $this->getExchangeRatesFromCurrency($fromCurrencyUpperCase);

        if (!isset($exchangeRatesForCurrency[$toCurrencyUpperCase])) {
            $this->loadExchangeRates($fromCurrency, [$toCurrency]);     
        }

        $exchangeRatesForCurrency = $this->getExchangeRatesFromCurrency($fromCurrencyUpperCase);

        if (!isset($exchangeRatesForCurrency[$toCurrencyUpperCase])) {
            throw new ExchangeRateNotFoundException();
        }

        return $exchangeRatesForCurrency[$toCurrencyUpperCase];
    }
}
