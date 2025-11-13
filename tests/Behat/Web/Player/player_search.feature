@player
Feature: Search Players
  In order to find players in the system
  As an authenticated user
  I want to search for players with filters

  Scenario: Search all players without filters
    Given I send a GET request to "/api/players"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players by username query
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440500" with body:
    """
    {
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "ProGamerSearch"
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/api/players?q=Gamer"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players by game id
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440501" with body:
    """
    {
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440005",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440101",
      "username": "LoLGamer"
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/api/players?gameId=550e8400-e29b-41d4-a716-446655440081"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players with both filters
    Given I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440502" with body:
    """
    {
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "ProValorantPlayer"
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/api/players?q=Pro&gameId=550e8400-e29b-41d4-a716-446655440080"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

