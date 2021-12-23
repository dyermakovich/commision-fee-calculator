<?php

declare(strict_types=1);

namespace DY\CFC\Tests\Service;

use DY\CFC\Service\Rounder;
use DY\CFC\Service\RounderInterface;
use PHPUnit\Framework\TestCase;

final class RounderTest extends TestCase
{
    private RounderInterface $rounder;

    protected function setUp(): void
    {
        $this->rounder = Rounder::create();
        parent::setUp();
    }

    public function testRounder(): void
    {
        $this->assertEquals(101, $this->rounder->roundUp(100.1));
        $this->assertEquals(1234, $this->rounder->roundUp(1234));
        $this->assertEquals(1235, $this->rounder->roundUp(1234.0001));
        $this->assertEquals(0.03, $this->rounder->roundUp(0.023, 2));
        $this->assertEquals(0.03, $this->rounder->roundUp(0.02001, 2));
        $this->assertEquals(0.02, $this->rounder->roundUp(0.020, 2));
        $this->assertEquals(0.003, $this->rounder->roundUp(0.0021, 3));
        $this->assertEquals(0.003, $this->rounder->roundUp(0.00201, 3));
        $this->assertEquals(0.004, $this->rounder->roundUp(0.00301, 3));
    }
}
