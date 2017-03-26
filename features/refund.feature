Feature: Capture

  Background:
    Given I have a card with "0" GBP in balance and "5" blocked

  Scenario: Merchant refund the captured amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to capture "2" GBP
    And I refund the captured amount of "2" GBP
    Then I have a card with "5" GBP in balance and "0" blocked
