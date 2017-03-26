<?php

namespace Domain\Transaction;

use Domain\Card\Card;
use Domain\Payee\Payee;
use Money\Money;

class Transaction
{
    /**
     * @var Card
     */
    private $card;

    /**
     * @var Money
     */
    private $amount;

    /**
     * @var Payee
     */
    private $payee;

    /**
     * @var Money
     */
    private $availableAmount;

    /**
     * @var Money
     */
    private $captured;

    /**
     * @var Money
     */
    private $authorisedAmount;

    /**
     * Transaction constructor.
     *
     * @param Card  $card
     * @param Money $amount
     * @param Money $availableAmount
     * @param Money $authorisedAmount
     * @param Payee $payee
     * @param Money $captured
     */
    public function __construct(
        Card $card,
        Money $amount,
        Money $availableAmount,
        Money $authorisedAmount,
        Payee $payee,
        Money $captured
    ) {
        $this->card = $card;
        $this->amount = $amount;
        $this->availableAmount = $availableAmount;
        $this->payee = $payee;
        $this->captured = $captured;
        $this->authorisedAmount = $authorisedAmount;
    }

    /**
     * @return Money
     */
    public function getAvailableAmount(): Money
    {
        return $this->availableAmount;
    }

    /**
     * @return Payee
     */
    public function getPayee(): Payee
    {
        return $this->payee;
    }

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return Money
     */
    public function getAuthorisedAmount(): Money
    {
        return $this->authorisedAmount;
    }

    /**
     * @param Money $amount
     *
     * @throws GreaterAmountException
     */
    public function capture(Money $amount)
    {
        if ($amount->greaterThan($this->availableAmount)) {
            throw new GreaterAmountException();
        }

        if ($amount->greaterThan($this->authorisedAmount)) {
            throw new GreaterAmountException();
        }

        $this->captured = $this->captured->add($amount);
        $this->availableAmount = $this->availableAmount->subtract($amount);
    }

    /**
     * @param Money $amount
     *
     * @throws GreaterAmountException
     */
    public function refund(Money $amount)
    {
        if ($amount->greaterThan($this->captured)) {
            throw new GreaterAmountException();
        }

        if ($amount->greaterThan($this->authorisedAmount)) {
            throw new GreaterAmountException();
        }

        $this->availableAmount = $this->availableAmount->add($amount);
    }

    /**
     * @param Money $amount
     *
     * @throws GreaterAmountException
     */
    public function reverse(Money $amount)
    {
        if ($amount->greaterThan($this->authorisedAmount)) {
            throw new GreaterAmountException();
        }

        $this->availableAmount = $this->availableAmount->subtract($amount);
        $this->card->reverse($amount);
    }
}
