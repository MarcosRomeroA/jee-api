Feature: Search Teams
  In order to find teams in the system
  As an authenticated user
  I want to search for teams with filters

  Scenario: Search all teams without filters
    Given I send a GET request to "/teams"
    Then the response status code should be 200

  Scenario: Search teams by name query
    Given I send a GET request to "/teams?q=Champions"
    Then the response status code should be 200

  Scenario: Search teams by game id
    Given I send a GET request to "/teams?gameId=650e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

  Scenario: Search teams with both filters
    Given I send a GET request to "/teams?q=Champions&gameId=650e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

