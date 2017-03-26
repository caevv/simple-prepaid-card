<?php

namespace Domain\Payee;

use Domain\Transaction\GreaterAmountException;
use Domain\Transaction\Transaction;
use Money\Money;

class Capture
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var bool
     */
    private $authorised = false;

    /**
     * @return bool
     */
    public function isAuthorised(): bool
    {
        return $this->authorised;
    }

    /**
     * Payee constructor.
     *
     * @param Transaction $transaction
     * @param Merchant    $merchant
     */
    public function __construct(Transaction $transaction, Merchant $merchant)
    {
        $this->transaction = $transaction;
        $this->merchant = $merchant;
    }

    /**
     * @param Money $amount
     *
     * @throws GreaterAmountException
     */
    public function capture(Money $amount)
    {
        if ($amount->greaterThan($this->transaction->getAvailableAmount())) {
            throw new GreaterAmountException();
        }

        $this->transaction->capture($amount);
        $this->transaction->getCard()->capture($amount);

        $this->authorised = true;

        $this->send($amount);
    }

    /**
     * @param Money $amount
     */
    private function send(Money $amount)
    {
        // Send amount to client.
    }

    /**
     * @param Money $amount
     *
     * @throws GreaterAmountException
     */
    public function refund(Money $amount)
    {
        if ($amount->greaterThan($this->transaction->getAvailableAmount())) {
            throw new GreaterAmountException();
        }

        $this->transaction->getCard()->refund($amount);
        $this->transaction->refund($amount);
    }
}
