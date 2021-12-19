<?php

declare(strict_types=1);

namespace DY\CFC\Tests;

use DY\CFC\CommissionFeeCalculatorApplication;
use PHPUnit\Framework\TestCase;

final class CommissionFeeCalculatorApplicationTest extends TestCase
{
    private function getInput(): string
    {
        return <<<END
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
2016-01-05,4,private,withdraw,1000.00,EUR
2016-01-05,1,private,deposit,200.00,EUR
2016-01-06,2,business,withdraw,300.00,EUR
2016-01-06,1,private,withdraw,30000,JPY
2016-01-07,1,private,withdraw,1000.00,EUR
2016-01-07,1,private,withdraw,100.00,USD
2016-01-10,1,private,withdraw,100.00,EUR
2016-01-10,2,business,deposit,10000.00,EUR
2016-01-10,3,private,withdraw,1000.00,EUR
2016-02-15,1,private,withdraw,300.00,EUR
2016-02-19,5,private,withdraw,3000000,JPY
END;
    }

    private function getOutput(): string
    {
        return <<<END
0.60
3.00
0.00
0.06
1.50
0
0.70
0.30
0.30
3.00
0.00
0.00
8612
END;
    }

    public function testApplication(): void
    {
        $this->assertEquals($this->getOutput(), CommissionFeeCalculatorApplication::create()->run($this->getInput()));
    }
}
