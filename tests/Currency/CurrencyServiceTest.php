<?php

declare(strict_types=1);

namespace DY\CFC\Tests\Currency;

use DY\CFC\Currency\Currency;
use DY\CFC\Currency\CurrencyService;
use DY\CFC\Currency\CurrencyServiceInterface;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use PHPUnit\Framework\TestCase;

final class CurrencyServiceTest extends TestCase
{
    private CurrencyServiceInterface $currencyService;

    /**
     * @throws ExchangeRatesLoadingException
     */
    public function setUp(): void
    {
        $this->currencyService = CurrencyService::createMock();
        parent::setUp();
    }

    public function testGetPrecision(): void
    {
        $this->assertEquals(0, $this->currencyService->getPrecision("1"));
        $this->assertEquals(0, $this->currencyService->getPrecision("1."));
        $this->assertEquals(1, $this->currencyService->getPrecision("1.1"));
        $this->assertEquals(2, $this->currencyService->getPrecision("1.11"));
        $this->assertEquals(0, $this->currencyService->getPrecision("1,11"));
    }

    public function testFormatEUR(): void
    {
        $eur = new Currency("EUR", 2);
        $this->assertEquals("0.03", $this->currencyService->format($eur, 0.031));
        $this->assertEquals("0.03", $this->currencyService->format($eur, 0.0301));
        $this->assertEquals("0.02", $this->currencyService->format($eur, 0.021));
    }

    public function testFormatJPY(): void
    {
        $eur = new Currency("JPY", 0);
        $this->assertEquals("101", $this->currencyService->format($eur, 101.1));
        $this->assertEquals("1234", $this->currencyService->format($eur, 1234));
    }

    /* public function testLoadExchangeRates()
    {
        $this->currencyService->findOrAddNew("EUR", 2);
        $this->currencyService->findOrAddNew("JPY", 0);
        $this->currencyService->findOrAddNew("USD", 2);
        $this->currencyService->loadExchangeRates();
    } */
}

