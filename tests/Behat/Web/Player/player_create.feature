@player
Feature: Create Player
  In order to register a player in the system
  As an authenticated user
  I want to create a new player profile

  Scenario: Successfully create a player
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440100" with body:
    """
    {
      "gameId": "550e8400-e29b-41d4-a716-446655440080",
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update existing player (UPSERT behavior)
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440101" with body:
    """
    {
      "gameId": "550e8400-e29b-41d4-a716-446655440080",
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "OriginalName"
    }
    """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440101" with body:
    """
    {
      "gameId": "550e8400-e29b-41d4-a716-446655440080",
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440002",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440013",
      "username": "UpdatedName"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create player with missing required fields
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440102" with body:
    """
    {
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 422

  Scenario: Create player with non-existent game role
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440103" with body:
    """
    {
      "gameId": "550e8400-e29b-41d4-a716-446655440080",
      "gameRoleId": "999e9999-e99b-99d9-a999-999999999999",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 404

  Scenario: Create player with non-existent game rank
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440104" with body:
    """
    {
      "gameId": "550e8400-e29b-41d4-a716-446655440080",
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "999e9999-e99b-99d9-a999-999999999999",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 404
