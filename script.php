<?php

declare(strict_types=1);

use DY\CFC\CommissionFeeCalculatorApplication;

require_once __DIR__ . '/vendor/autoload.php';

// define("EXCHANGERATES_API_KEY", "ef461b886b59c1f369a2f75642875a5c");

echo CommissionFeeCalculatorApplication::create()->processMultiline(file_get_contents($argv[1]));
