@game @auth
Feature: Find All Game Roles
  In order to get all roles for a specific game
  As an authenticated user
  I want to retrieve a list of all roles for a game

  Scenario: Get all roles for Valorant
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/roles"
    Then the response status code should be 200
    And the response should have a "data" property
    And the "data" property should be an array containing objects with properties "id, roleId, roleName, roleDescription"

  Scenario: Try to get roles without authentication
    When I send a GET request to "/api/game/550e8400-e29b-41d4-a716-446655440080/roles"
    Then the response status code should be 401
