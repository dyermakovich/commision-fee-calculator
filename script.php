<?php

declare(strict_types=1);

use DY\CFC\CommissionFeeCalculatorApplication;

require_once __DIR__ . '/vendor/autoload.php';

echo CommissionFeeCalculatorApplication::create()->run(file_get_contents($argv[1]));
