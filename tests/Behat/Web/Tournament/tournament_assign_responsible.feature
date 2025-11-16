@tournament @auth
Feature: Assign Responsible to Tournament
  In order to delegate tournament management
  As a tournament responsible
  I want to assign a new responsible to a tournament

  Scenario: Successfully assign a new responsible to tournament
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000/responsible/550e8400-e29b-41d4-a716-446655440002" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Assign responsible to non-existent tournament
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999/responsible/550e8400-e29b-41d4-a716-446655440002" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Assign non-existent user as responsible
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000/responsible/999e9999-e99b-99d9-a999-999999999999" with body:
      """
      {}
      """
    Then the response status code should be 404
