@game
Feature: Search Games
  In order to find games in the system
  As an authenticated user
  I want to search for games with filters

  Scenario: Search all games without filters
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/games"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search games by name query
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/games?q=League"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
