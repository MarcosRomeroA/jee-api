@team @auth
Feature: Team Games Management
  In order to manage team games
  As an authenticated user
  I want to add, remove and list team games

  Scenario: Successfully add a game to a team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440060/game/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Add a game that does not exist
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440060/game/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Add a game to a team that does not exist
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/999e9999-e99b-99d9-a999-999999999999/game/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 404

  Scenario: Successfully remove a game from a team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/team/550e8400-e29b-41d4-a716-446655440060/game/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Remove a game that does not exist in team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/team/550e8400-e29b-41d4-a716-446655440060/game/550e8400-e29b-41d4-a716-446655440003"
    Then the response status code should be 404

  Scenario: Successfully find all games for a team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/team/550e8400-e29b-41d4-a716-446655440060/games"
    Then the response status code should be 200
    And the response should be an array
