<?php

declare(strict_types=1);

namespace DY\CFC\Tests\Operation;

use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Currency\CurrencyService;
use DY\CFC\Currency\CurrencyServiceInterface;
use DY\CFC\Exception\UnexpectedException;
use DY\CFC\Operation\OperationAbstract;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationService;
use DY\CFC\Operation\OperationServiceInterface;
use DY\CFC\Operation\Withdraw;
use DY\CFC\User\User;
use DY\CFC\User\UserInterface;
use DY\CFC\User\UserService;
use DY\CFC\User\UserServiceInterface;
use PHPUnit\Framework\TestCase;

class WithdrawTest extends TestCase
{
    private UserServiceInterface $userService;
    private CurrencyServiceInterface $currencyService;
    private OperationServiceInterface $operationService;

    protected function setUp(): void
    {
        $this->userService = UserService::create();
        $this->currencyService = CurrencyService::create();
        $this->operationService = OperationService::create();

        parent::setUp();
    }

    private function createCurrencyEUR(): CurrencyInterface
    {
        return $this->currencyService->findOrAddNew("EUR", 2);
    }

    private function createUser(): UserInterface
    {
        return $this->userService->findOrAddNew("4", User::PRIVATE);
    }

    private function createWithdraw1(): OperationInterface
    {
        return $this->operationService->addNew(
            "2014-12-31",
            OperationAbstract::WITHDRAW,
            "1200.00",
            $this->createCurrencyEUR(),
            $this->createUser()
        );
    }

    private function createWithdraw2(): OperationInterface
    {
        return $this->operationService->addNew(
            "2015-01-01",
            OperationAbstract::WITHDRAW,
            "1000.00",
            $this->createCurrencyEUR(),
            $this->createUser()
        );
    }

    /**
     * @throws UnexpectedException
     */
    public function testWithdraw(): void
    {
        $withdraw = $this->createWithdraw1();
        $this->assertInstanceOf(Withdraw::class, $withdraw);
        $this->assertEquals("2014-12-29", $withdraw->getTheNearestMondayAsString());
        $this->assertEquals(0, $withdraw->getWithdrawCountDuringThisWeek());
        $this->assertEquals(0, $withdraw->getWithdrawAmountDuringThisWeek());
        $this->assertEquals(1000, $withdraw->getMaxFreeOfChargeWithdrawAmountPerWeek());
        $this->assertEquals(200, $withdraw->getAmountForCharge());

        $withdraw = $this->createWithdraw2();
        $this->assertInstanceOf(Withdraw::class, $withdraw);
        $this->assertEquals("2014-12-29", $withdraw->getTheNearestMondayAsString());
        $this->assertEquals(1, $withdraw->getWithdrawCountDuringThisWeek());
        $this->assertEquals(1200, $withdraw->getWithdrawAmountDuringThisWeek());
        $this->assertEquals(1000, $withdraw->getAmountForCharge());
    }
}
