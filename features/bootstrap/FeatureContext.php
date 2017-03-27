<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Domain\Card\Card;
use Domain\Payee\Capture;
use Domain\Payee\Merchant;
use Domain\Payee\Reverse;
use Domain\Transaction\GreaterAmountException;
use Domain\Transaction\Transaction;
use Money\Money;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var Domain\Card\Card
     */
    private $card;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Capture
     */
    private $capture;

    /**
     * @Transform :amount
     */
    public function makeAmountGBP($amount)
    {
        return Money::GBP($amount);
    }

    /**
     * @Transform :amountAvailable
     */
    public function makeAvailableGBPAmount($amount)
    {
        return $this->makeAmountGBP($amount);
    }

    /**
     * @Transform :amountBlocked
     */
    public function makeBlockedGBPAmount($amount)
    {
        return $this->makeAmountGBP($amount);
    }

    /**
     * @Given I have a card with :amount GBP in balance
     */
    public function iHaveACardWithGBPInBalance(Money $amount)
    {
        $this->card = new Card($amount, $this->makeAmountGBP(0));
    }

    /**
     * @When I top up :amount pounds on my card
     */
    public function iTopUpPoundsOnMyCard(Money $amount)
    {
        $this->card->topUp($amount);
    }

    /**
     * @Then I should have :amount pounds on my balance
     */
    public function iShouldHavePoundsOnMyBalance(Money $amount)
    {
        Assert::assertInstanceOf(Money::class, $this->card->getAvailableBalance());

        $this->myAvailableBalanceShouldBeGbp($amount);
    }

    /**
     * @When I go to a shop and ask to buy something for :amount GBP with my card
     */
    public function iGoToAShopAndAskToBuySomethingForGbpWithMyCard(Money $amount)
    {
        $this->transaction = $this->card->buy($amount, new Merchant());
    }

    /**
     * @Then I should be authorized to buy :amount GBP
     */
    public function iShouldBeAuthorizedToBuyGBP(Money $amount)
    {
        Assert::assertEquals($amount->getAmount(), $this->transaction->getAuthorisedAmount()->getAmount());
    }

    /**
     * @Then My available balance should be :amount GBP
     */
    public function myAvailableBalanceShouldBeGbp(Money $amount)
    {
        Assert::assertEquals($amount->getAmount(), $this->card->getAvailableBalance()->getAmount());
    }

    /**
     * @Then My blocked balance should be :amount GBP
     */
    public function myBlockedBalanceShouldBeGbp(Money $amount)
    {
        Assert::assertEquals($amount->getAmount(), $this->card->getBlockedBalance()->getAmount());
    }

    /**
     * @Given I have a card with :amountAvailable GBP in balance and :amountBlocked blocked
     */
    public function iHaveACardWithGBPInBalanceAndBlocked(Money $amountAvailable, Money $amountBlocked)
    {
        $this->card = new Card($amountAvailable, $amountBlocked);
    }

    /**
     * @Given I had a card transaction with the amount of :amount GBP
     */
    public function iHadACardTransactionWithTheAmountOfGbp(Money $amount)
    {
        $this->transaction = new Domain\Transaction\Transaction(
            $this->card,
            $amount,
            $amount,
            $amount,
            new Merchant(),
            $this->makeAmountGBP($amount)
        );
    }

    /**
     * @When I request to capture :amount GBP
     */
    public function iRequestToCaptureGBP(Money $amount)
    {
        $this->capture = new Capture($this->transaction, new Merchant());

        try {
            $this->capture->capture($amount);
        } catch (GreaterAmountException $greaterAmountException) {
        }
    }

    /**
     * @Then I should have the amount sent to me
     */
    public function iShouldHaveTheAmountSentToMe()
    {
        // Amount send automatically after capturing.
        Assert::assertTrue($this->capture->isAuthorised());
    }

    /**
     * @Given The card should have :amount GBP marked as blocked
     */
    public function theCardShouldHaveGBPMarkedAsBlocked(Money $amount)
    {
        Assert::assertEquals($amount->getAmount(), $this->card->getBlockedBalance()->getAmount());
    }

    /**
     * @Then I should not be authorised
     */
    public function iShouldNotBeAuthorised()
    {
        Assert::assertNotTrue($this->capture->isAuthorised());
    }

    /**
     * @Given I refund the captured amount of :amount GBP
     */
    public function iRefundTheCapturedAmountOfGBP(Money $amount)
    {
        $this->capture->refund($amount);
    }

    /**
     * @When I request to reverse :amount GBP
     */
    public function iRequestToReverseGBP(Money $amount)
    {
        $reverse = new Reverse($this->transaction);
        $reverse->reverse($amount);
    }

    /**
     * @Given The card should have :amount GBP available
     */
    public function theCardShouldHaveGBPAvailable(Money $amount)
    {
        Assert::assertEquals($amount->getAmount(), $this->card->getAvailableBalance()->getAmount());
    }
}
