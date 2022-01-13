<?php

declare(strict_types=1);

namespace DY\CFC\Tests;

use DY\CFC\CommissionFeeCalculatorApplication;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    private CommissionFeeCalculatorApplication $app;

    /**
     * @throws Exception
     */
    public function getApplication(): CommissionFeeCalculatorApplication
    {
        if (!isset($this->app)) {
            $this->app = CommissionFeeCalculatorApplication::create(true);
        }
        return $this->app;
    }
}
