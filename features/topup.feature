Feature: Top up card

Scenario: Top up new card
  Given I have a card with "0" GBP in balance
  When I top up "50" pounds on my card
  Then I should have "50" pounds on my balance