<?php

declare(strict_types=1);

namespace DY\CFC\Operation;

use DateTimeInterface;
use DY\CFC\Currency\CurrencyInterface;
use DY\CFC\Operation\Strategy\FeeStrategyInterface;
use DY\CFC\Operation\Strategy\OperationStrategyInterface;
use DY\CFC\Service\RounderInterface;
use DY\CFC\User\UserInterface;

class Operation implements OperationInterface
{
    private ?OperationInterface $previous;
    private ?OperationStrategyInterface $strategy;
    private ?FeeStrategyInterface $feeStrategy;

    public function __construct(
        private DateTimeInterface $date,
        private float $amount,
        private CurrencyInterface $currency,
        private UserInterface $user,
        private RounderInterface $rounder
    ) {
        $this->previous = $user->getLastOperation();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getType(): string
    {
        return $this->strategy->getType();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getAmountInBaseCurrency(): float
    {
        return $this->currency->convertToBaseCurrency($this->getAmount());
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getFee(): float
    {
        return $this->feeStrategy->getFee();
    }

    public function getPrevious(): ?OperationInterface
    {
        return $this->previous;
    }

    public function getAmountForCharge(): float
    {
        return $this->strategy->getAmountForCharge();
    }

    public function getCommissionRate(): float
    {
        return $this->strategy->getCommissionRate();
    }

    public function setStrategy(OperationStrategyInterface $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function setFeeStrategy(FeeStrategyInterface $strategy): self
    {
        $this->feeStrategy = $strategy;
        return $this;
    }
}
