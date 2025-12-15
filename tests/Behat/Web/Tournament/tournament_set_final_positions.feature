@tournament @auth
Feature: Set Tournament Final Positions
  In order to finalize a tournament
  As a tournament creator or responsible
  I want to define the final positions (1st, 2nd, 3rd place)

  Scenario: Tournament responsible successfully sets final positions
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create three teams first (before tournament so they can be added)
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "name": "First Place Team"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "name": "Second Place Team"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440012" with body:
      """
      {
        "name": "Third Place Team"
      }
      """
    Then the response status code should be 200
    # Create a tournament with ACTIVE status
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Finals Tournament",
        "description": "A tournament to test final positions",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-01-01T10:00:00Z",
        "endAt": "2026-01-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Add teams to tournament (as responsible)
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440001/team/970e8400-e29b-41d4-a716-446655440010" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440001/team/970e8400-e29b-41d4-a716-446655440011" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440001/team/970e8400-e29b-41d4-a716-446655440012" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Set final positions
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440001/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440010",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655440011",
        "thirdPlaceTeamId": "970e8400-e29b-41d4-a716-446655440012"
      }
      """
    Then the response status code should be 200

  Scenario: Successfully set only first place (2nd and 3rd are optional)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create one team
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440050" with body:
      """
      {
        "name": "Winner Team"
      }
      """
    Then the response status code should be 200
    # Create a tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440005" with body:
      """
      {
        "name": "Small Tournament",
        "description": "A tournament with only one winner",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-05-01T10:00:00Z",
        "endAt": "2026-05-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Add team to tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440005/team/970e8400-e29b-41d4-a716-446655440050" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Set only first place
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440005/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440050"
      }
      """
    Then the response status code should be 200

  Scenario: Successfully set first and second place (3rd is optional)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create two teams
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440060" with body:
      """
      {
        "name": "Gold Team"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440061" with body:
      """
      {
        "name": "Silver Team"
      }
      """
    Then the response status code should be 200
    # Create a tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440006" with body:
      """
      {
        "name": "Two Finalists Tournament",
        "description": "A tournament with only two finalists",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-06-01T10:00:00Z",
        "endAt": "2026-06-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Add teams to tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440006/team/970e8400-e29b-41d4-a716-446655440060" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440006/team/970e8400-e29b-41d4-a716-446655440061" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Set first and second place only
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440006/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440060",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655440061"
      }
      """
    Then the response status code should be 200

  Scenario: Unauthorized user cannot set final positions
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create teams first
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440020" with body:
      """
      {
        "name": "Team Alpha"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440021" with body:
      """
      {
        "name": "Team Beta"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440022" with body:
      """
      {
        "name": "Team Gamma"
      }
      """
    Then the response status code should be 200
    # Create a tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Unauthorized Finals Tournament",
        "description": "Testing unauthorized access",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-02-01T10:00:00Z",
        "endAt": "2026-02-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Add teams to tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440002/team/970e8400-e29b-41d4-a716-446655440020" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440002/team/970e8400-e29b-41d4-a716-446655440021" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440002/team/970e8400-e29b-41d4-a716-446655440022" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Another user tries to set final positions
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440002/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440020",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655440021",
        "thirdPlaceTeamId": "970e8400-e29b-41d4-a716-446655440022"
      }
      """
    Then the response status code should be 403

  Scenario: Cannot set final positions with unregistered team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create teams first (only register two later)
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440030" with body:
      """
      {
        "name": "Registered Team 1"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440031" with body:
      """
      {
        "name": "Registered Team 2"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/970e8400-e29b-41d4-a716-446655440032" with body:
      """
      {
        "name": "Unregistered Team"
      }
      """
    Then the response status code should be 200
    # Create a tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Unregistered Team Tournament",
        "description": "Testing with unregistered team",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-03-01T10:00:00Z",
        "endAt": "2026-03-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Only add two teams to tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440003/team/970e8400-e29b-41d4-a716-446655440030" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440003/team/970e8400-e29b-41d4-a716-446655440031" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Try to set final positions with unregistered team
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440003/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440030",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655440031",
        "thirdPlaceTeamId": "970e8400-e29b-41d4-a716-446655440032"
      }
      """
    Then the response status code should be 404

  Scenario: Cannot set final positions for non-existent tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655449999/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655440010",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655440011",
        "thirdPlaceTeamId": "970e8400-e29b-41d4-a716-446655440012"
      }
      """
    Then the response status code should be 404

  Scenario: Cannot set final positions with non-existent team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a tournament
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "name": "Non-Existent Team Tournament",
        "description": "Testing with non-existent team",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-04-01T10:00:00Z",
        "endAt": "2026-04-10T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Try to set final positions with non-existent team
    When I send a PUT request to "/api/tournament/970e8400-e29b-41d4-a716-446655440004/final-positions" with body:
      """
      {
        "firstPlaceTeamId": "970e8400-e29b-41d4-a716-446655449991",
        "secondPlaceTeamId": "970e8400-e29b-41d4-a716-446655449992",
        "thirdPlaceTeamId": "970e8400-e29b-41d4-a716-446655449993"
      }
      """
    Then the response status code should be 404
