<?php
namespace Domain\Card;

use Domain\Payee\Payee;
use Domain\Payee\TopUp;
use Domain\Transaction\Transaction;
use Money\Money;

class Card
{
    /**
     * @var Money
     */
    private $availableBalance;

    /**
     * @var Money
     */
    private $blockedBalance;

    /**
     * Card constructor.
     *
     * @param Money $availableBalance
     * @param Money $blockedBalance
     */
    public function __construct(Money $availableBalance, Money $blockedBalance)
    {
        $this->availableBalance = $availableBalance;
        $this->blockedBalance = $blockedBalance;
    }

    /**
     * @return Money
     */
    public function getAvailableBalance(): Money
    {
        return $this->availableBalance;
    }

    /**
     * @return Money
     */
    public function getBlockedBalance(): Money
    {
        return $this->blockedBalance;
    }

    /**
     * @param Money $amount
     */
    public function topUp(Money $amount)
    {
        new Transaction($this, $amount, $amount, $amount, new TopUp(), Money::GB(0));

        $this->availableBalance = $this->availableBalance->add($amount);
    }

    /**
     * @param Money $amount
     * @param Payee $payee
     *
     * @return Transaction
     *
     * @throws NotEnoughBalanceException
     */
    public function buy(Money $amount, Payee $payee): Transaction
    {
        if ($this->availableBalance->lessThan($amount)) {
            throw new NotEnoughBalanceException();
        }

        $this->availableBalance = $this->availableBalance->subtract($amount);
        $this->blockedBalance = $this->blockedBalance->add($amount);

        return new Transaction($this, $amount, $amount, $amount, $payee, Money::GBP(0));

    }

    /**
     * @param Money $amount
     */
    public function capture(Money $amount)
    {
        $this->blockedBalance = $this->blockedBalance->subtract($amount);
    }

    /**
     * @param Money $amount
     */
    public function refund(Money $amount)
    {
        $this->blockedBalance = $this->blockedBalance->subtract($amount);
        $this->availableBalance = $this->availableBalance->add($amount);
    }

    /**
     * @param Money $amount
     */
    public function reverse(Money $amount)
    {
        $this->blockedBalance = $this->blockedBalance->subtract($amount);
        $this->availableBalance = $this->availableBalance->add($amount);
    }
}
