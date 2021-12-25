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
use DY\CFC\Operation\OperationType;
use DY\CFC\Operation\Withdraw;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use DY\CFC\Tests\MockExchangeRateLoader;
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

    /**
     * @throws ExchangeRatesLoadingException
     */
    protected function setUp(): void
    {
        $this->userService = UserService::create();
        $this->currencyService = CurrencyService::create(MockExchangeRateLoader::create());
        $this->operationService = OperationService::create();

        parent::setUp();
    }

    private function createCurrencyEUR(): CurrencyInterface
    {
        return $this->currencyService->findOrAddNew("EUR", 2);
    }

    private function createCurrencyJPY(): CurrencyInterface
    {
        return $this->currencyService->findOrAddNew("JPY", 0);
    }

    private function createUser4(): UserInterface
    {
        return $this->userService->findOrAddNew("4", User::PRIVATE);
    }

    private function createUser1(): UserInterface
    {
        return $this->userService->findOrAddNew("1", User::PRIVATE);
    }

    private function createUser6(): UserInterface
    {
        return $this->userService->findOrAddNew("6", User::PRIVATE);
    }

    private function createWithdraw1(): OperationInterface
    {
        return $this->operationService->addNew(
            "2014-12-31",
            OperationType::WITHDRAW,
            "1200.00",
            $this->createCurrencyEUR(),
            $this->createUser4()
        );
    }

    private function createWithdraw2(): OperationInterface
    {
        return $this->operationService->addNew(
            "2015-01-01",
            OperationType::WITHDRAW,
            "1000.00",
            $this->createCurrencyEUR(),
            $this->createUser4()
        );
    }

    private function createWithdraw3(): OperationInterface
    {
        return $this->operationService->addNew(
            "2016-01-05",
            OperationType::WITHDRAW,
            "1000.00",
            $this->createCurrencyEUR(),
            $this->createUser4()
        );
    }

    private function createWithdraw4(): OperationInterface
    {
        return $this->operationService->addNew(
            "2016-01-06",
            OperationType::WITHDRAW,
            "30000",
            $this->createCurrencyJPY(),
            $this->createUser1()
        );
    }

    /**
     * @throws UnexpectedException
     */
    public function testWithdraw(): void
    {
        $withdraw1 = $this->createWithdraw1();
        $this->assertInstanceOf(Withdraw::class, $withdraw1);
        $this->assertEquals("2014-12-29", $withdraw1->getTheNearestMondayAsString());
        $this->assertEquals(0, $withdraw1->getWithdrawCountDuringThisWeek());
        $this->assertEquals(0, $withdraw1->getWithdrawAmountDuringThisWeekInEuro());
        $this->assertEquals(1000, $withdraw1->getMaxFreeOfChargeWithdrawAmountPerWeekInEuro());
        $this->assertEquals(200, $withdraw1->getAmountForCharge());
        $this->assertEquals(0.6, $withdraw1->getFee());

        $withdraw2 = $this->createWithdraw2();
        $this->assertInstanceOf(Withdraw::class, $withdraw2);
        $this->assertEquals("2014-12-29", $withdraw2->getTheNearestMondayAsString());
        $this->assertEquals(1, $withdraw2->getWithdrawCountDuringThisWeek());
        $this->assertEquals(1200, $withdraw2->getWithdrawAmountDuringThisWeekInEuro());
        $this->assertEquals(1000, $withdraw2->getAmountForCharge());
        $this->assertEquals(3, $withdraw2->getFee());

        $withdraw3 = $this->createWithdraw3();
        $this->assertInstanceOf(Withdraw::class, $withdraw3);
        $this->assertEquals("2016-01-04", $withdraw3->getTheNearestMondayAsString());
        $this->assertEquals(0, $withdraw3->getWithdrawCountDuringThisWeek());
        $this->assertEquals(0, $withdraw3->getWithdrawAmountDuringThisWeekInEuro());
        $this->assertEquals(0, $withdraw3->getAmountForCharge());
        $this->assertEquals(0, $withdraw3->getFee());

        $withdraw4 = $this->createWithdraw4();
        $this->assertInstanceOf(Withdraw::class, $withdraw4);
        $this->assertEquals("2016-01-04", $withdraw4->getTheNearestMondayAsString());
        $this->assertEquals(0, $withdraw4->getWithdrawCountDuringThisWeek());
        $this->assertEquals(0, $withdraw4->getWithdrawAmountDuringThisWeekInEuro());
        $this->assertEquals(0, $withdraw4->getAmountForCharge());
        $this->assertEquals(0, $withdraw4->getFee());
    }

    public function testWeekFreeOfCharge(): void
    {
        $withdraw1 = $this->operationService->addNew(
            "2021-12-21",
            OperationType::WITHDRAW,
            "100",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(0, $withdraw1->getFee());

        $withdraw2 = $this->operationService->addNew(
            "2021-12-22",
            OperationType::WITHDRAW,
            "100",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(0, $withdraw2->getFee());

        $withdraw3 = $this->operationService->addNew(
            "2021-12-23",
            OperationType::WITHDRAW,
            "100",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(0, $withdraw3->getFee());

        $withdraw4 = $this->operationService->addNew(
            "2021-12-24",
            OperationType::WITHDRAW,
            "1000",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(3, $withdraw4->getFee());

        $withdraw5 = $this->operationService->addNew(
            "2021-12-27",
            OperationType::WITHDRAW,
            "500",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(0, $withdraw5->getFee());

        $withdraw6 = $this->operationService->addNew(
            "2021-12-27",
            OperationType::WITHDRAW,
            "1000",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(1.5, $withdraw6->getFee());

        $withdraw7 = $this->operationService->addNew(
            "2021-12-27",
            OperationType::WITHDRAW,
            "1000",
            $this->createCurrencyEUR(),
            $this->createUser6()
        );

        $this->assertEquals(3, $withdraw7->getFee());
    }
}
