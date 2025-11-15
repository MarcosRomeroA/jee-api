@game @auth
Feature: Search Game Ranks
  In order to get all ranks for a specific game
  As an authenticated user
  I want to retrieve a list of all ranks for a game without pagination

  Scenario: Get all ranks for Valorant
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440081/ranks"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have at least 20 items

  Scenario: Get all ranks for League of Legends
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/ranks"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have at least 25 items

  Scenario: Get all ranks for Counter-Strike 2
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440082/ranks"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have at least 15 items

  Scenario: Get all ranks for Dota 2
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440083/ranks"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have at least 30 items

  Scenario: Try to get ranks without authentication
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/ranks"
    Then the response status code should be 401
