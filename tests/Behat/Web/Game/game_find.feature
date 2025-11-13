@game
Feature: Find Game
  In order to get game information
  As an authenticated user
  I want to retrieve a game by id

  Scenario: Successfully find a game by id
    Given I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080"
    Then the response status code should be 200
    And the response should have property "id" with value "550e8400-e29b-41d4-a716-446655440080"
    And the response should have property "name"
    And the response should have property "minPlayersQuantity"
    And the response should have property "maxPlayersQuantity"

  Scenario: Find game with non-existent id
    Given I send a GET request to "/api/game/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find game with invalid id format
    Given I send a GET request to "/api/game/invalid-id"
    Then the response status code should be 400
