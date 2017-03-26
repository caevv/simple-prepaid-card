Feature: Reverse transaction

  Background:
    Given I have a card with "0" GBP in balance and "5" blocked

  Scenario: Reverse full transaction amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to reverse "5" GBP
    Then The card should have "0" GBP marked as blocked
    And The card should have "5" GBP available

  Scenario: Reverse partial transaction amount
    Given I had a card transaction with the amount of "5" GBP
    When I request to reverse "3" GBP
    Then The card should have "2" GBP marked as blocked
    And The card should have "3" GBP available
