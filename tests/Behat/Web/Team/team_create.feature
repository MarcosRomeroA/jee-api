@team
Feature: Create Team
  In order to form gaming teams
  As an authenticated user
  I want to create a new team

  Scenario: Successfully create a team
    Given I send a POST request to "/api/team" with body:
      """
      {
        "id": "550e8400-e29b-41d4-a716-446655440060",
        "gameId": "550e8400-e29b-41d4-a716-446655440002",
        "ownerId": "550e8400-e29b-41d4-a716-446655440001",
        "name": "Pro Gamers Team",
        "image": "https://example.com/team-logo.png"
      }
      """
    Then the response status code should be 200
    And the response should be empty

