Feature: Buy in-store

  Scenario: Buy from a merchant
    Given I have a card with "100" GBP in balance
    When I go to a shop and ask to buy something for "5" GBP with my card
    Then I should be authorized to buy "5" GBP
    And My available balance should be "95" GBP
    And My blocked balance should be "5" GBP
