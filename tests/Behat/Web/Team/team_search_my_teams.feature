Feature: Search My Teams
  In order to find my teams
  As an authenticated user
  I want to search for my teams with filters

  Scenario: Search all my teams without filters
    Given I send a GET request to "/my-teams?userId=650e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my teams by name query
    Given I send a GET request to "/my-teams?userId=650e8400-e29b-41d4-a716-446655440002&q=Champions"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my teams by game id
    Given I send a GET request to "/my-teams?userId=650e8400-e29b-41d4-a716-446655440002&gameId=650e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search my teams with both filters
    Given I send a GET request to "/my-teams?userId=650e8400-e29b-41d4-a716-446655440002&q=Champions&gameId=650e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

