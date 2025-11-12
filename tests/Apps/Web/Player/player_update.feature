@player
Feature: Update Player
  In order to modify player information
  As an authenticated user
  I want to update a player profile

  Scenario: Successfully update a player
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "username": "UpdatedGamer456",
      "gameRoleId": "550e8400-e29b-41d4-a716-446655440005",
      "gameRankId": "550e8400-e29b-41d4-a716-446655440006"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update player with non-existent id
    Given I send a PUT request to "/api/player/999e9999-e99b-99d9-a999-999999999999" with body:
    """
    {
      "username": "NonExistent",
      "gameRoleId": "550e8400-e29b-41d4-a716-446655440005",
      "gameRankId": "550e8400-e29b-41d4-a716-446655440006"
    }
    """
    Then the response status code should be 404

  Scenario: Update player with missing required fields
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "username": "UpdatedGamer456"
    }
    """
    Then the response status code should be 422

