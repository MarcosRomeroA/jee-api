@team @auth
Feature: Create Team
  In order to form gaming teams
  As an authenticated user
  I want to create a new team

  Scenario: Successfully create a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440070" with body:
      """
      {
        "name": "Pro Gamers Team",
        "description": "A professional gaming team",
        "image": "https://example.com/team-logo.png"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with empty description
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440075" with body:
      """
      {
        "name": "Another Team",
        "description": "",
        "image": "https://example.com/team-logo2.png"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team without optional fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440076" with body:
      """
      {
        "name": "Minimal Team"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440071" with body:
      """
      {
        "description": "Missing name field",
        "image": "https://example.com/logo.png"
      }
      """
    Then the response status code should be 422
