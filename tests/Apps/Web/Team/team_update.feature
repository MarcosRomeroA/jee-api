Feature: Update Team
  In order to modify team information
  As an authenticated user
  I want to update a team

  Scenario: Successfully update a team
    Given I send a PUT request to "/team/650e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "name": "Updated Champions",
      "image": "https://example.com/new-team.jpg"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update team with non-existent id
    Given I send a PUT request to "/team/999e9999-e99b-99d9-a999-999999999999" with body:
    """
    {
      "name": "NonExistent Team",
      "image": "https://example.com/team.jpg"
    }
    """
    Then the response status code should be 404

  Scenario: Update team with missing required fields
    Given I send a PUT request to "/team/650e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "image": "https://example.com/team.jpg"
    }
    """
    Then the response status code should be 400

  Scenario: Update team with invalid id format
    Given I send a PUT request to "/team/invalid-id" with body:
    """
    {
      "name": "Updated Team"
    }
    """
    Then the response status code should be 400

