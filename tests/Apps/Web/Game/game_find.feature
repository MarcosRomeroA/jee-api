Feature: Find Game
  In order to get game information
  As an authenticated user
  I want to retrieve a game by id

  Scenario: Successfully find a game by id
    Given I send a GET request to "/game/850e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "id": "850e8400-e29b-41d4-a716-446655440000",
      "name": "League of Legends",
      "description": "5v5 MOBA game",
      "minPlayersQuantity": 1,
      "maxPlayersQuantity": 5
    }
    """

  Scenario: Find game with non-existent id
    Given I send a GET request to "/game/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find game with invalid id format
    Given I send a GET request to "/game/invalid-id"
    Then the response status code should be 400
Feature: Search Games
  In order to find games in the system
  As an authenticated user
  I want to search for games with filters

  Scenario: Search all games without filters
    Given I send a GET request to "/games"
    Then the response status code should be 200

  Scenario: Search games by name query
    Given I send a GET request to "/games?q=League"
    Then the response status code should be 200

