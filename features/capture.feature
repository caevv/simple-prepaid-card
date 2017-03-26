Feature: Capture

  Background:
    Given I have a card with "0" GBP in balance and "5" blocked

  Scenario: Merchant capture card whole amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to capture "5" GBP
    Then I should have the amount sent to me
    And The card should have "0" GBP marked as blocked

  Scenario: Merchant capture part of the amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to capture "2" GBP
    Then I should have the amount sent to me
    And The card should have "3" GBP marked as blocked

  Scenario: Merchant request more than the transaction amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to capture "10" GBP
    Then I should not be authorised
    And The card should have "5" GBP marked as blocked
