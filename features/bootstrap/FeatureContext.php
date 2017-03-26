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
     * @var Merchant
     */
    private $merchant;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Capture
     */
    private $capture;

    /**
     * @Given I have a card with :amount GBP in balance
     */
    public function iHaveACardWithGBPInBalance($amount)
    {
        $this->card = new Card(Money::GBP($amount), Money::GBP(0));
    }

    /**
     * @When I top up :amount pounds on my card
     */
    public function iTopUpPoundsOnMyCard($amount)
    {
        $this->card->topUp(Money::GBP($amount));
    }

    /**
     * @Then I should have :amount pounds on my balance
     */
    public function iShouldHavePoundsOnMyBalance($amount)
    {
        Assert::assertInstanceOf(Money::class, $this->card->getAvailableBalance());

        $this->myAvailableBalanceShouldBeGbp($amount);
    }

    /**
     * @When I go to a shop and ask to buy something for :amount GBP with my card
     */
    public function iGoToAShopAndAskToBuySomethingForGbpWithMyCard($amount)
    {
        $this->transaction = $this->card->buy(Money::GBP($amount), new Merchant());
    }

    /**
     * @Then I should be authorized to buy :amount GBP
     */
    public function iShouldBeAuthorizedToBuyGBP($amount)
    {
        Assert::assertEquals($amount, $this->transaction->getAuthorisedAmount()->getAmount());
    }

    /**
     * @Then My available balance should be :amount GBP
     */
    public function myAvailableBalanceShouldBeGbp($amount)
    {
        Assert::assertEquals($amount, $this->card->getAvailableBalance()->getAmount());
    }

    /**
     * @Then My blocked balance should be :amount GBP
     */
    public function myBlockedBalanceShouldBeGbp($amount)
    {
        Assert::assertEquals($amount, $this->card->getBlockedBalance()->getAmount());
    }

    /**
     * @Given I have a card with :amountAvailable GBP in balance and :amountBlocked blocked
     */
    public function iHaveACardWithGBPInBalanceAndBlocked($amountAvailable, $amountBlocked)
    {
        $this->card = new Card(Money::GBP($amountAvailable), Money::GBP($amountBlocked));
    }

    /**
     * @Given I had a card transaction with the amount of :amount GBP
     */
    public function iHadACardTransactionWithTheAmountOfGbp($amount)
    {
        $this->transaction = new Domain\Transaction\Transaction(
            $this->card,
            Money::GBP($amount),
            Money::GBP($amount),
            Money::GBP($amount),
            new Merchant(),
            Money::GBP(0)
        );
    }

    /**
     * @When I request to capture :amount GBP
     */
    public function iRequestToCaptureGBP($amount)
    {
        $this->capture = new Capture($this->transaction, new Merchant());

        try {
            $this->capture->capture(Money::GBP($amount));
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
    public function theCardShouldHaveGBPMarkedAsBlocked($amount)
    {
        Assert::assertEquals($amount, $this->card->getBlockedBalance()->getAmount());
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
    public function iRefundTheCapturedAmountOfGBP($amount)
    {
        $this->capture->refund(Money::GBP($amount));
    }

    /**
     * @When I request to reverse :amount GBP
     */
    public function iRequestToReverseGBP($amount)
    {
        $reverse = new Reverse($this->transaction);
        $reverse->reverse(Money::GBP($amount));
    }

    /**
     * @Given The card should have :amount GBP available
     */
    public function theCardShouldHaveGBPAvailable($amount)
    {
        Assert::assertEquals($amount, $this->card->getAvailableBalance()->getAmount());
    }
}
