Feature: Delete Tournament
  In order to remove a tournament from the system
  As an authenticated user
  I want to delete a tournament

  Scenario: Successfully delete a tournament
    Given I send a DELETE request to "/tournament/750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete tournament with non-existent id
    Given I send a DELETE request to "/tournament/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Delete tournament with invalid id format
    Given I send a DELETE request to "/tournament/invalid-id"
    Then the response status code should be 400
Feature: Create Tournament
  In order to organize a tournament in the system
  As an authenticated user
  I want to create a new tournament

  Scenario: Successfully create a tournament
    Given I send a POST request to "/tournament" with body:
    """
    {
      "id": "750e8400-e29b-41d4-a716-446655440000",
      "gameId": "750e8400-e29b-41d4-a716-446655440001",
      "responsibleId": "750e8400-e29b-41d4-a716-446655440002",
      "name": "Summer Championship 2025",
      "description": "Annual summer tournament",
      "maxTeams": 16,
      "isOfficial": true,
      "image": "https://example.com/tournament.jpg",
      "prize": "$10,000",
      "region": "NA",
      "startAt": "2025-06-01 10:00:00",
      "endAt": "2025-06-30 20:00:00"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create tournament with missing required fields
    Given I send a POST request to "/tournament" with body:
    """
    {
      "id": "750e8400-e29b-41d4-a716-446655440000",
      "name": "Summer Championship 2025"
    }
    """
    Then the response status code should be 400

  Scenario: Create tournament with invalid game id
    Given I send a POST request to "/tournament" with body:
    """
    {
      "id": "750e8400-e29b-41d4-a716-446655440000",
      "gameId": "invalid-uuid",
      "responsibleId": "750e8400-e29b-41d4-a716-446655440002",
      "name": "Summer Championship 2025",
      "maxTeams": 16,
      "isOfficial": false,
      "startAt": "2025-06-01 10:00:00",
      "endAt": "2025-06-30 20:00:00"
    }
    """
    Then the response status code should be 400

