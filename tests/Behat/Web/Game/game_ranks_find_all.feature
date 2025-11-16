@game @auth
Feature: Find All Game Ranks
  In order to get all ranks for a specific game
  As an authenticated user
  I want to retrieve a list of all ranks for a game

  Scenario: Get all ranks for Valorant
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/ranks"
    Then the response status code should be 200
    And the response should have a "data" property
    And the "data" property should be an array containing objects with properties "id, rankId, rankName, level"

  Scenario: Try to get ranks without authentication
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/ranks"
    Then the response status code should be 401
