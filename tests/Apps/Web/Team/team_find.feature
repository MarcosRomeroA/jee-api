Feature: Find Team
  In order to get team information
  As an authenticated user
  I want to retrieve a team by id

  Scenario: Successfully find a team by id
    Given I send a GET request to "/team/650e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "id": "650e8400-e29b-41d4-a716-446655440000",
      "name": "The Champions",
      "image": "https://example.com/team.jpg",
      "gameId": "650e8400-e29b-41d4-a716-446655440001",
      "ownerId": "650e8400-e29b-41d4-a716-446655440002",
      "playersCount": 0
    }
    """

  Scenario: Find team with non-existent id
    Given I send a GET request to "/team/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find team with invalid id format
    Given I send a GET request to "/team/invalid-id"
    Then the response status code should be 400

