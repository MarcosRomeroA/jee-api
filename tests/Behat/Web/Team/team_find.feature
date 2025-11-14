@team @auth
Feature: Find Team
  In order to get team information
  As an authenticated user
  I want to retrieve a team by id

  Scenario: Successfully find a team by id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/team/550e8400-e29b-41d4-a716-446655440060"
    Then the response status code should be 200

  Scenario: Find team with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/team/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404


