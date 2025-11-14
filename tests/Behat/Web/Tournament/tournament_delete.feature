@tournament @auth
Feature: Delete Tournament
  In order to remove a tournament from the system
  As an authenticated user
  I want to delete a tournament

  Scenario: Successfully delete a tournament
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete tournament with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404



