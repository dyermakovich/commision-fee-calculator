<?php

declare(strict_types=1);

use DY\CFC\CommissionFeeCalculatorApplication;
use DY\CFC\Exception\IncorrectInputException;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;

require_once __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
    die("As first argument provide input CSV file name to process.");
}

if (!isset($argv[2])) {
    die("As second argument provide API key for exchangeratesapi.io.");
}

define("EXCHANGE_RATES_API_KEY", $argv[2] ?? "");

try {
    echo CommissionFeeCalculatorApplication::create()->processMultiline(file_get_contents($argv[1]));
} catch (IncorrectInputException $e) {
    die("Incorrect input data detected, check content of CSV file.");
} catch (ExchangeRatesLoadingException $e) {
    die(sprintf('Problem with loading data from API: "%s".', $e->getMessage()));
}
