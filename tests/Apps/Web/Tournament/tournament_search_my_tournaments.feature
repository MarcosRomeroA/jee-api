Feature: Search My Tournaments
  In order to find my tournaments
  As an authenticated user
  I want to search for my tournaments with filters

  Scenario: Search all my tournaments without filters
    Given I send a GET request to "/my-tournaments?responsibleId=750e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200

  Scenario: Search my tournaments by name query
    Given I send a GET request to "/my-tournaments?responsibleId=750e8400-e29b-41d4-a716-446655440002&q=Championship"
    Then the response status code should be 200

  Scenario: Search my tournaments by game id
    Given I send a GET request to "/my-tournaments?responsibleId=750e8400-e29b-41d4-a716-446655440002&gameId=750e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

  Scenario: Search my tournaments with both filters
    Given I send a GET request to "/my-tournaments?responsibleId=750e8400-e29b-41d4-a716-446655440002&q=Championship&gameId=750e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200

