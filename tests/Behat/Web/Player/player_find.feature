@player
Feature: Find Player
  In order to get player information
  As an authenticated user
  I want to retrieve a player by id

  Scenario: Successfully find a player by id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440300" with body:
      """
      {
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
        "username": "ProGamer123"
      }
      """
    Then the response status code should be 200
    When I send a GET request to "/api/player/550e8400-e29b-41d4-a716-446655440300"
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "username" property
    And the response should have "verified" property

  Scenario: Find player with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/player/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find player with invalid id format
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/player/invalid-id"
    Then the response status code should be 400
