@team
Feature: Delete Team
  In order to remove a team from the system
  As an authenticated user
  I want to delete a team

  Scenario: Successfully delete a team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/team/550e8400-e29b-41d4-a716-446655440060"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete team with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/team/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Delete team with invalid id format
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a DELETE request to "/api/team/invalid-id"
    Then the response status code should be 400




