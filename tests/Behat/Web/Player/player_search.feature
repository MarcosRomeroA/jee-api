@player @auth
Feature: Search Players
  In order to find players in the system
  As an authenticated user
  I want to search for players with filters

  Scenario: Search all players without filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/players"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players by username query
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/players?q=Gamer"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players by game id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/players?gameId=550e8400-e29b-41d4-a716-446655440081"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search players with both filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/players?q=Pro&gameId=550e8400-e29b-41d4-a716-446655440080"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my players
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/players?mine=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
