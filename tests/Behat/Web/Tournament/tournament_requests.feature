@tournament @auth
Feature: Tournament Requests
  In order to manage tournament join requests
  As an authenticated user
  I want to view and manage pending tournament requests

  Scenario: Request access to a tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Test Tournament",
        "description": "A test tournament",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-01-15T10:00:00Z",
        "endAt": "2026-01-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "name": "Test Team Alpha"
      }
      """
    Then the response status code should be 200
    # Request access to the tournament with the team
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440001/team/850e8400-e29b-41d4-a716-446655440010/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200

  Scenario: Get pending tournament requests
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Championship Tournament",
        "description": "Annual championship",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 8,
        "startAt": "2026-02-15T10:00:00Z",
        "endAt": "2026-02-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "name": "Championship Team"
      }
      """
    Then the response status code should be 200
    # Request access
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440002/team/850e8400-e29b-41d4-a716-446655440011/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get pending requests
    When I send a GET request to "/api/tournament/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    And the JSON node "requests[0].status" should be equal to "pending"
    And the JSON node "requests[0].teamName" should be equal to "Championship Team"

  Scenario: Accept a tournament request
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Pro League Tournament",
        "description": "Professional league",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 4,
        "startAt": "2026-03-15T10:00:00Z",
        "endAt": "2026-03-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440012" with body:
      """
      {
        "name": "Pro Team"
      }
      """
    Then the response status code should be 200
    # Request access
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440003/team/850e8400-e29b-41d4-a716-446655440012/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get the request ID
    When I send a GET request to "/api/tournament/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    And I save the value of JSON node "requests[0].id" as "requestId"
    # Accept the request
    When I send a PUT request to "/api/tournament/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Verify no pending requests remain
    When I send a GET request to "/api/tournament/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements

  Scenario: Cannot request access twice
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "name": "Double Request Tournament",
        "description": "Testing duplicate requests",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 8,
        "startAt": "2026-04-15T10:00:00Z",
        "endAt": "2026-04-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440013" with body:
      """
      {
        "name": "Duplicate Team"
      }
      """
    Then the response status code should be 200
    # First request
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440004/team/850e8400-e29b-41d4-a716-446655440013/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Second request should fail
    When I send a PUT request to "/api/tournament/850e8400-e29b-41d4-a716-446655440004/team/850e8400-e29b-41d4-a716-446655440013/request-access" with body:
      """
      {}
      """
    Then the response status code should be 409

  Scenario: Empty pending requests list when no requests exist
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements
