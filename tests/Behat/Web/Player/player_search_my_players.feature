@player
Feature: Search My Players
  In order to find my own players
  As an authenticated user
  I want to search for my players with filters

  Scenario: Search all my players without filters
    Given I send a PUT request to "/api/player/650e8400-e29b-41d4-a716-446655440600" with body:
    """
    {
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "MyPlayerOne"
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/api/my-players"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my players by username query
    Given I send a PUT request to "/api/player/650e8400-e29b-41d4-a716-446655440601" with body:
    """
    {
      "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
      "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
      "username": "MyUniquePlayer"
    }
    """
    Then the response status code should be 200
    When I send a GET request to "/api/my-players?q=Unique"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my players with pagination
    Given I send a GET request to "/api/my-players?page=1&limit=5"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And the response metadata should have "limit" property with value "5"

