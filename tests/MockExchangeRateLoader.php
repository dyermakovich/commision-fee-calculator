<?php

namespace DY\CFC\Tests;

use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use DY\CFC\Service\ExchangeRateLoader;
use DY\CFC\Service\ExchangeRateLoaderInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class MockExchangeRateLoader
{
    private function __construct()
    {
    }

    /**
     * @throws ExchangeRatesLoadingException
     */
    public static function create(): ExchangeRateLoaderInterface
    {
        $response = new MockResponse(
            <<<END
            {
                "success":true,
                "timestamp":1640202843,
                "base":"EUR",
                "date":"2021-12-22",
                "rates":{
                    "USD":1.1497,
                    "JPY":129.53
                }
            }
            END
        );

        return ExchangeRateLoader::create(new MockHttpClient([$response]));
    }
}

