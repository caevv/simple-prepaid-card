<?php
namespace Domain\Payee;

use Domain\Transaction\Transaction;
use Money\Money;

class Reverse
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * Reverse constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param Money $amount
     */
    public function reverse(Money $amount)
    {
        $this->transaction->reverse($amount);
    }
}
