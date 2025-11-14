@team @auth
Feature: Create Team
  In order to form gaming teams
  As an authenticated user
  I want to create a new team

  Scenario: Successfully create a team
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440070" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440002",
        "ownerId": "550e8400-e29b-41d4-a716-446655440001",
        "name": "Pro Gamers Team",
        "image": "https://example.com/team-logo.png"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update an existing team (upsert)
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440060" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440002",
        "ownerId": "550e8400-e29b-41d4-a716-446655440001",
        "name": "Updated Team Name",
        "image": "https://example.com/updated-logo.png"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with missing required fields
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440071" with body:
      """
      {
        "name": "Incomplete Team"
      }
      """
    Then the response status code should be 422


  Scenario: Create team with non-existent game
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440072" with body:
      """
      {
        "gameId": "999e9999-e99b-99d9-a999-999999999999",
        "ownerId": "550e8400-e29b-41d4-a716-446655440001",
        "name": "Test Team"
      }
      """
    Then the response status code should be 404


