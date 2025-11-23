@tournament @auth
Feature: Find Tournament
  In order to get tournament information
  As an authenticated user
  I want to retrieve a tournament by id

  Scenario: Successfully find a tournament by id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "name" property
    And the response should have "description" property
    And the response should have "rules" property

  Scenario: Find tournament with non-existent id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404
