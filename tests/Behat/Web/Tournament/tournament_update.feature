@tournament @auth
Feature: Update Tournament
  In order to modify tournament information
  As an authenticated user
  I want to update a tournament

  Scenario: Successfully update a tournament
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "name": "Updated Championship 2025",
      "description": "Updated description",
      "maxTeams": 32,
      "isOfficial": true,
      "image": "https://example.com/new-tournament.jpg",
      "prize": "$20,000",
      "region": "EU",
      "startAt": "2025-07-01 10:00:00",
      "endAt": "2025-07-31 20:00:00"
    }
    """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update tournament with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999" with body:
    """
    {
      "name": "NonExistent Tournament",
      "maxTeams": 16,
      "isOfficial": false,
      "startAt": "2025-06-01 10:00:00",
      "endAt": "2025-06-30 20:00:00"
    }
    """
    Then the response status code should be 404

  Scenario: Update tournament with missing required fields
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000" with body:
    """
    {
      "description": "Only description"
    }
    """
    Then the response status code should be 422


