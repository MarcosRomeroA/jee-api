@team @auth
Feature: Search Teams
  In order to find teams in the system
  As an authenticated user
  I want to search for teams with filters

  Scenario: Search all teams without filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/teams"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search teams by name (partial match)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "750e8400-e29b-41d4-a716-446655440020" exists with name "UniqueSearchTest Squad" created by "tester1@test.com"
    When I send a GET request to "/api/teams?name=UniqueSearchTest"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And the response "metadata.total" should be 1
    And the response "data[0].name" should be "UniqueSearchTest Squad"

  Scenario: Search teams by name returns empty when no match
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/teams?name=NonExistentTeamName12345"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response "metadata.total" should be 0

  Scenario: Search teams by game id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/teams?gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search teams with name and gameId filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/teams?name=Gaming&gameId=550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my teams
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/teams?mine=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  @tournament
  Scenario: Search teams by tournament id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "750e8400-e29b-41d4-a716-446655440001" exists with name "Team In Tournament"
    And the team "750e8400-e29b-41d4-a716-446655440001" is registered in tournament "750e8400-e29b-41d4-a716-446655440000"
    And a team "750e8400-e29b-41d4-a716-446655440002" exists with name "Team Not In Tournament"
    When I send a GET request to "/api/teams?tournamentId=750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And the response "metadata.total" should be 1
    And the response "data[0].name" should be "Team In Tournament"
