<?php

declare(strict_types=1);

namespace DY\CFC\Tests\Currency;

use DY\CFC\Currency\Currency;
use DY\CFC\Currency\CurrencyService;
use DY\CFC\Currency\CurrencyServiceInterface;
use PHPUnit\Framework\TestCase;

final class CurrencyServiceTest extends TestCase
{
    private CurrencyServiceInterface $currencyService;

    public function setUp(): void
    {
        $this->currencyService = CurrencyService::create();

        if(!defined("EXCHANGERATES_API_KEY")) {
            define("EXCHANGERATES_API_KEY", "ef461b886b59c1f369a2f75642875a5c");
        }

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
        $this->assertEquals("0.03", $this->currencyService->format($eur, 0.023));
        $this->assertEquals("0.03", $this->currencyService->format($eur, 0.02001));
        $this->assertEquals("0.02", $this->currencyService->format($eur, 0.020));
    }

    public function testFormatJPY(): void
    {
        $eur = new Currency("JPY", 0);
        $this->assertEquals("101", $this->currencyService->format($eur, 100.1));
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

