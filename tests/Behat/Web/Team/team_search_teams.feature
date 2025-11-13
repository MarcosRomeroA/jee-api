@team
Feature: Search Teams
  In order to find teams in the system
  As an authenticated user
  I want to search for teams with filters

  Scenario: Search all teams without filters
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/teams"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search teams by name query
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/teams?q=Gaming"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search teams by game id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/teams?gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search teams with both filters
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/teams?q=Gaming&gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

