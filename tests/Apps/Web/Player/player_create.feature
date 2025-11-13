Feature: Create Player
  In order to register a player in the system
  As an authenticated user
  I want to create a new player profile

  Scenario: Successfully create a player
    Given I send a POST request to "/api/player" with body:
    """
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "userId": "550e8400-e29b-41d4-a716-446655440001",
      "gameId": "550e8400-e29b-41d4-a716-446655440002",
      "gameRoleIds": ["550e8400-e29b-41d4-a716-446655440003"],
      "gameRankId": "550e8400-e29b-41d4-a716-446655440004",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully create a player with multiple roles
    Given I send a POST request to "/api/player" with body:
    """
    {
      "id": "550e8400-e29b-41d4-a716-446655440007",
      "userId": "550e8400-e29b-41d4-a716-446655440001",
      "gameId": "550e8400-e29b-41d4-a716-446655440002",
      "gameRoleIds": ["550e8400-e29b-41d4-a716-446655440003", "550e8400-e29b-41d4-a716-446655440005"],
      "gameRankId": "550e8400-e29b-41d4-a716-446655440004",
      "username": "MultiRolePlayer"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully create a player without rank
    Given I send a POST request to "/api/player" with body:
    """
    {
      "id": "550e8400-e29b-41d4-a716-446655440008",
      "userId": "550e8400-e29b-41d4-a716-446655440001",
      "gameId": "550e8400-e29b-41d4-a716-446655440002",
      "gameRoleIds": ["550e8400-e29b-41d4-a716-446655440003"],
      "username": "NoRankPlayer"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create player with missing required fields
    Given I send a POST request to "/api/player" with body:
    """
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 422

  Scenario: Create player with invalid user id
    Given I send a POST request to "/api/player" with body:
    """
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "userId": "invalid-uuid",
      "gameId": "550e8400-e29b-41d4-a716-446655440002",
      "gameRoleIds": ["550e8400-e29b-41d4-a716-446655440003"],
      "gameRankId": "550e8400-e29b-41d4-a716-446655440004",
      "username": "ProGamer123"
    }
    """
    Then the response status code should be 404


