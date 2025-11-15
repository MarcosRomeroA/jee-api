@game @auth
Feature: Search Game Roles
  In order to get all roles for a specific game
  As an authenticated user
  I want to retrieve a list of all roles for a game without pagination

  Scenario: Get all roles for Valorant
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/roles"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have 4 items

  Scenario: Get all roles for League of Legends
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440081/roles"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have 5 items

  Scenario: Get all roles for Counter-Strike 2
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440082/roles"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have 5 items

  Scenario: Get all roles for Dota 2
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440083/roles"
    Then the response status code should be 200
    And the response should be a valid JSON array
    And the response should have 5 items

  Scenario: Try to get roles without authentication
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/roles"
    Then the response status code should be 401
