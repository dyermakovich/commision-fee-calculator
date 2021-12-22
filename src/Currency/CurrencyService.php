<?php

declare(strict_types=1);

namespace DY\CFC\Currency;

use DY\CFC\Currency\Exception\UnsupportedCurrencyPrecisionException;
use DY\CFC\Service\Parser;
use DY\CFC\Service\ParserInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyService implements CurrencyServiceInterface
{
    private array $currencies = [];

    public function __construct(
        private ParserInterface $parser,
        private HttpClientInterface $client
    ) {
    }

    public static function create(): CurrencyServiceInterface
    {
        return new CurrencyService(Parser::create(), HttpClient::create());
    }

    public function getCurrenciesSymbols(): array
    {
        $result = array();

        /**
         * @var CurrencyInterface $currency
         */
        foreach ($this->currencies as $currency) {
            $result[] = $currency->getName();
        }

        return $result;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function loadExchangeRates(): void
    {
        $response = $this->client->request(
            "GET",
            sprintf(
                "http://api.exchangeratesapi.io/latest?access_key=%s&base=EUR&symbols=%s",
                EXCHANGERATES_API_KEY,
                implode(",", $this->getCurrenciesSymbols())
            )
        );

        $data = json_decode($response->getContent());

        foreach (get_object_vars($data->rates) as $symbol => $rate) {
            $parsedRate = $this->parser->parseFloat($rate, 1);
            $this->findByName($symbol)?->setExchangeRateFromEuro($parsedRate);
        }
    }

    /**
     * @throws UnsupportedCurrencyPrecisionException
     */
    public function format(CurrencyInterface $currency, float $amount): string
    {
        if ($currency->getPrecision() === 0) {
            return sprintf("%d", ceil($amount));
        }

        if ($currency->getPrecision() === 2) {
            return sprintf("%.2f", ceil($amount * 100) / 100);
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

    public function findOrAddNew(string $name, int $precision): CurrencyInterface
    {
        $uppercaseName = strtoupper($name);
        if (!isset($this->currencies[$uppercaseName])) {
            $this->currencies[$uppercaseName] = new Currency($uppercaseName, $precision);
        }
        return $this->currencies[$uppercaseName];
    }
}
