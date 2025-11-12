Feature: Find Tournament
  In order to get tournament information
  As an authenticated user
  I want to retrieve a tournament by id

  Scenario: Successfully find a tournament by id
    Given I send a GET request to "/tournament/750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "id": "750e8400-e29b-41d4-a716-446655440000",
      "name": "Summer Championship 2025",
      "description": "Annual summer tournament",
      "gameId": "750e8400-e29b-41d4-a716-446655440001",
      "responsibleId": "750e8400-e29b-41d4-a716-446655440002",
      "registeredTeams": 0,
      "maxTeams": 16,
      "isOfficial": true
    }
    """

  Scenario: Find tournament with non-existent id
    Given I send a GET request to "/tournament/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find tournament with invalid id format
    Given I send a GET request to "/tournament/invalid-id"
    Then the response status code should be 400

