@tournament @auth
Feature: Leave Tournament
  In order to manage my team's tournament participation
  As a team creator or leader
  I want to be able to withdraw my team from a tournament

  Scenario: Team creator successfully leaves a tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Leave Test Tournament",
        "description": "A tournament to test leaving",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-05-15T10:00:00Z",
        "endAt": "2026-05-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "name": "Leave Test Team"
      }
      """
    Then the response status code should be 200
    # Add team to tournament directly (as tournament responsible)
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440001/team/960e8400-e29b-41d4-a716-446655440010" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Leave the tournament
    When I send a POST request to "/api/tournament/960e8400-e29b-41d4-a716-446655440001/team/960e8400-e29b-41d4-a716-446655440010/leave" with body:
      """
      {}
      """
    Then the response status code should be 200

  Scenario: Non-creator/non-leader cannot withdraw team from tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Unauthorized Leave Tournament",
        "description": "Testing unauthorized leave",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-06-15T10:00:00Z",
        "endAt": "2026-06-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "name": "Unauthorized Leave Team"
      }
      """
    Then the response status code should be 200
    # Add team to tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440002/team/960e8400-e29b-41d4-a716-446655440011" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Another user tries to leave the tournament with this team
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a POST request to "/api/tournament/960e8400-e29b-41d4-a716-446655440002/team/960e8400-e29b-41d4-a716-446655440011/leave" with body:
      """
      {}
      """
    Then the response status code should be 403

  Scenario: Cannot leave a tournament the team is not registered in
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Not Registered Tournament",
        "description": "Testing not registered",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-07-15T10:00:00Z",
        "endAt": "2026-07-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create a team but don't register in tournament
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440012" with body:
      """
      {
        "name": "Not Registered Team"
      }
      """
    Then the response status code should be 200
    # Try to leave without being registered
    When I send a POST request to "/api/tournament/960e8400-e29b-41d4-a716-446655440003/team/960e8400-e29b-41d4-a716-446655440012/leave" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Cannot leave a non-existent tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a team
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440013" with body:
      """
      {
        "name": "Team For Non-Existent Tournament"
      }
      """
    Then the response status code should be 200
    # Try to leave non-existent tournament
    When I send a POST request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999/team/960e8400-e29b-41d4-a716-446655440013/leave" with body:
      """
      {}
      """
    Then the response status code should be 404
