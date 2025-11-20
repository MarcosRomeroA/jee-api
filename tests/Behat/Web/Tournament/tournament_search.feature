@tournament @auth
Feature: Search Tournaments
  In order to find tournaments in the system
  As an authenticated user
  I want to search for tournaments with filters

  Scenario: Search all tournaments without filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournaments"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search tournaments by name query
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournaments?q=Championship"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search tournaments by game id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournaments?gameId=750e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my tournaments
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournaments?mine=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search open tournaments
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournaments?open=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
