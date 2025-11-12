@player
Feature: Search Players
  In order to find players in the system
  As an authenticated user
  I want to search for players with filters

  Scenario: Search all players without filters
    Given I send a GET request to "/api/players"
    Then the response status code should be 200

  Scenario: Search players by username query
    Given I send a GET request to "/api/players?q=Gamer"
    Then the response status code should be 200

  Scenario: Search players by game id
    Given I send a GET request to "/api/players?gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200

  Scenario: Search players with both filters
    Given I send a GET request to "/api/players?q=Pro&gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200

