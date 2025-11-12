Feature: Search Open Tournaments
  In order to find tournaments to join
  As an authenticated user
  I want to search for open tournaments with filters

  Scenario: Search all open tournaments without filters
    Given I send a GET request to "/open-tournaments"
    Then the response status code should be 200

  Scenario: Search open tournaments by name query
    Given I send a GET request to "/open-tournaments?q=Championship"
    Then the response status code should be 200

  Scenario: Search open tournaments by game id
    Given I send a GET request to "/open-tournaments?gameId=750e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

  Scenario: Search open tournaments with both filters
    Given I send a GET request to "/open-tournaments?q=Championship&gameId=750e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

