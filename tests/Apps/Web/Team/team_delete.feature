Feature: Delete Team
  In order to remove a team from the system
  As an authenticated user
  I want to delete a team

  Scenario: Successfully delete a team
    Given I send a DELETE request to "/team/650e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete team with non-existent id
    Given I send a DELETE request to "/team/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Delete team with invalid id format
    Given I send a DELETE request to "/team/invalid-id"
    Then the response status code should be 400
Feature: Create Team
  In order to register a team in the system
  As an authenticated user
  I want to create a new team

  Scenario: Successfully create a team
    Given I send a POST request to "/team" with body:
    """
    {
      "id": "650e8400-e29b-41d4-a716-446655440000",
      "gameId": "650e8400-e29b-41d4-a716-446655440001",
      "ownerId": "650e8400-e29b-41d4-a716-446655440002",
      "name": "The Champions",
      "image": "https://example.com/team.jpg"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with missing required fields
    Given I send a POST request to "/team" with body:
    """
    {
      "id": "650e8400-e29b-41d4-a716-446655440000",
      "name": "The Champions"
    }
    """
    Then the response status code should be 400

  Scenario: Create team with invalid game id
    Given I send a POST request to "/team" with body:
    """
    {
      "id": "650e8400-e29b-41d4-a716-446655440000",
      "gameId": "invalid-uuid",
      "ownerId": "650e8400-e29b-41d4-a716-446655440002",
      "name": "The Champions"
    }
    """
    Then the response status code should be 400

