<?php

declare(strict_types=1);

namespace DY\CFC\Tests\Operation;

use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Currency\CurrencyService;
use DY\CFC\Currency\CurrencyServiceInterface;
use DY\CFC\Exception\UnexpectedException;
use DY\CFC\Operation\Operation;
use DY\CFC\Operation\OperationInterface;
use DY\CFC\Operation\OperationService;
use DY\CFC\Operation\OperationServiceInterface;
use DY\CFC\Operation\OperationType;
use DY\CFC\Operation\Strategy\SearchOperationStrategyInterface;
use DY\CFC\Operation\Strategy\WithdrawStrategy;
use DY\CFC\Service\Exception\ExchangeRatesLoadingException;
use DY\CFC\User\User;
use DY\CFC\User\UserInterface;
use DY\CFC\User\UserService;
use DY\CFC\User\UserServiceInterface;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;
use Throwable;

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
        $this->currencyService = CurrencyService::createMock();
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

    private function getWithdrawStrategy(OperationInterface $operation): WithdrawStrategy
    {
        $strategyProperty = new ReflectionProperty(Operation::class, 'strategy');
        $strategyProperty->setAccessible(true);
        return $strategyProperty->getValue($operation);
    }

    private function getSearchOperationStrategy(OperationInterface $operation): SearchOperationStrategyInterface
    {
        $strategy = $this->getWithdrawStrategy($operation);
        $searchStrategyProperty = new ReflectionProperty(WithdrawStrategy::class, 'searchOperationStrategy');
        $searchStrategyProperty->setAccessible(true);
        return $searchStrategyProperty->getValue($strategy);
    }

    /**
     * @throws UnexpectedException
     */
    private function getTheNearestMondayAsString(OperationInterface $operation): string
    {
        return $this->getSearchOperationStrategy($operation)->getTheNearestMondayAsString($operation);
    }

    /**
     * @throws UnexpectedException
     */
    private function invokeWithdrawStrategyPrivateMethod(
        OperationInterface $operation,
        string $methodName
    ): mixed {
        try {
            $method = new ReflectionMethod(WithdrawStrategy::class, $methodName);
            $method->setAccessible(true);
            return $method->invoke($this->getWithdrawStrategy($operation));
        } catch (Throwable $throwable) {
            throw new UnexpectedException(previous: $throwable);
        }
    }

    /**
     * @throws UnexpectedException
     */
    private function getWithdrawCountDuringThisWeek(OperationInterface $operation): int
    {
        return $this->invokeWithdrawStrategyPrivateMethod($operation, 'getWithdrawCountDuringThisWeek');
    }

    /**
     * @throws UnexpectedException
     */
    private function getWithdrawAmountDuringThisWeekInBaseCurrency(OperationInterface $operation): float
    {
        return $this->invokeWithdrawStrategyPrivateMethod(
            $operation,
            'getWithdrawAmountDuringThisWeekInBaseCurrency'
        );
    }

    /**
     * @throws UnexpectedException
     */
    private function getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency(OperationInterface $operation): float
    {
        return $this->invokeWithdrawStrategyPrivateMethod(
            $operation,
            'getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency'
        );
    }

    /**
     * @throws UnexpectedException
     */
    public function testWithdraw(): void
    {
        $withdraw1 = $this->createWithdraw1();
        $this->assertEquals("2014-12-29", $this->getTheNearestMondayAsString($withdraw1));
        $this->assertEquals(0, $this->getWithdrawCountDuringThisWeek($withdraw1));
        $this->assertEquals(0, $this->getWithdrawAmountDuringThisWeekInBaseCurrency($withdraw1));
        $this->assertEquals(1000, $this->getMaxFreeOfChargeWithdrawAmountPerWeekInBaseCurrency($withdraw1));
        $this->assertEquals(200, $withdraw1->getAmountForCharge());
        $this->assertEquals(0.6, $withdraw1->getFee());

        $withdraw2 = $this->createWithdraw2();
        $this->assertEquals("2014-12-29", $this->getTheNearestMondayAsString($withdraw2));
        $this->assertEquals(1, $this->getWithdrawCountDuringThisWeek($withdraw2));
        $this->assertEquals(1200, $this->getWithdrawAmountDuringThisWeekInBaseCurrency($withdraw2));
        $this->assertEquals(1000, $withdraw2->getAmountForCharge());
        $this->assertEquals(3, $withdraw2->getFee());

        $withdraw3 = $this->createWithdraw3();
        $this->assertEquals("2016-01-04", $this->getTheNearestMondayAsString($withdraw3));
        $this->assertEquals(0, $this->getWithdrawCountDuringThisWeek($withdraw3));
        $this->assertEquals(0, $this->getWithdrawAmountDuringThisWeekInBaseCurrency($withdraw3));
        $this->assertEquals(0, $withdraw3->getAmountForCharge());
        $this->assertEquals(0, $withdraw3->getFee());

        $withdraw4 = $this->createWithdraw4();
        $this->assertEquals("2016-01-04", $this->getTheNearestMondayAsString($withdraw4));
        $this->assertEquals(0, $this->getWithdrawCountDuringThisWeek($withdraw4));
        $this->assertEquals(0, $this->getWithdrawAmountDuringThisWeekInBaseCurrency($withdraw4));
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
