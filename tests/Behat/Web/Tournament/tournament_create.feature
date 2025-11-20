@tournament @auth
Feature: Create Tournament
  In order to organize tournaments
  As an authenticated user
  I want to create a new tournament

  Scenario: Successfully create a tournament with all fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440100" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Summer Championship 2025",
        "description": "A competitive summer tournament",
        "maxTeams": 16,
        "isOfficial": true,
        "image": "https://example.com/tournament.jpg",
        "prize": "$1000",
        "region": "LATAM",
        "startAt": "2025-06-01T10:00:00Z",
        "endAt": "2025-06-30T20:00:00Z"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create tournament with minimal required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440101" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Minimal Tournament",
        "isOfficial": false
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create tournament using sessionId as responsibleId
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440102" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "My Tournament",
        "isOfficial": true
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create tournament with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/750e8400-e29b-41d4-a716-446655440103" with body:
      """
      {
        "description": "Missing name and game"
      }
      """
    Then the response status code should be 422
