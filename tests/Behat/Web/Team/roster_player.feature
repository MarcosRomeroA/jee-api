@team @auth @roster
Feature: Roster Player Management
  In order to manage players in a roster
  As a team creator or leader
  I want to add and remove players from rosters

  Scenario: Successfully add a player to a roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440001" exists with name "Player Roster Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440001" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440001" exists for team "860e8400-e29b-41d4-a716-446655440001" with game "550e8400-e29b-41d4-a716-446655440080" and name "Test Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440001"
    And a player "a60e8400-e29b-41d4-a716-446655440001" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/860e8400-e29b-41d4-a716-446655440001/roster/960e8400-e29b-41d4-a716-446655440001/player/b60e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "playerId": "a60e8400-e29b-41d4-a716-446655440001",
        "isStarter": true,
        "isLeader": false
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Add player with game role
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440002" exists with name "Role Roster Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440002" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440002" exists for team "860e8400-e29b-41d4-a716-446655440002" with game "550e8400-e29b-41d4-a716-446655440080" and name "Role Test Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440002"
    And a player "a60e8400-e29b-41d4-a716-446655440002" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/860e8400-e29b-41d4-a716-446655440002/roster/960e8400-e29b-41d4-a716-446655440002/player/b60e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "playerId": "a60e8400-e29b-41d4-a716-446655440002",
        "isStarter": true,
        "isLeader": true,
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440001"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Add player fails when player is not from a team member
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440003" exists with name "Non Member Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440003" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440003" exists for team "860e8400-e29b-41d4-a716-446655440003" with game "550e8400-e29b-41d4-a716-446655440080" and name "Non Member Roster"
    And a player "a60e8400-e29b-41d4-a716-446655440003" exists for user "tester2@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/860e8400-e29b-41d4-a716-446655440003/roster/960e8400-e29b-41d4-a716-446655440003/player/b60e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "playerId": "a60e8400-e29b-41d4-a716-446655440003",
        "isStarter": false,
        "isLeader": false
      }
      """
    Then the response status code should be 409

  Scenario: Add player fails when roster already has 5 starters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440004" exists with name "Full Starters Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440004" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440004" exists for team "860e8400-e29b-41d4-a716-446655440004" with game "550e8400-e29b-41d4-a716-446655440080" and name "Full Starters Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440004"
    And roster "960e8400-e29b-41d4-a716-446655440004" has 5 starters
    And a player "a60e8400-e29b-41d4-a716-446655440004" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/860e8400-e29b-41d4-a716-446655440004/roster/960e8400-e29b-41d4-a716-446655440004/player/b60e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "playerId": "a60e8400-e29b-41d4-a716-446655440004",
        "isStarter": true,
        "isLeader": false
      }
      """
    Then the response status code should be 409

  Scenario: Add player fails when roster already has a leader
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440005" exists with name "Has Leader Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440005" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440005" exists for team "860e8400-e29b-41d4-a716-446655440005" with game "550e8400-e29b-41d4-a716-446655440080" and name "Has Leader Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440005"
    And roster "960e8400-e29b-41d4-a716-446655440005" has a leader
    And a player "a60e8400-e29b-41d4-a716-446655440005" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/860e8400-e29b-41d4-a716-446655440005/roster/960e8400-e29b-41d4-a716-446655440005/player/b60e8400-e29b-41d4-a716-446655440005" with body:
      """
      {
        "playerId": "a60e8400-e29b-41d4-a716-446655440005",
        "isStarter": false,
        "isLeader": true
      }
      """
    Then the response status code should be 409

  Scenario: Successfully remove a player from a roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440006" exists with name "Remove Player Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440006" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440006" exists for team "860e8400-e29b-41d4-a716-446655440006" with game "550e8400-e29b-41d4-a716-446655440080" and name "Remove Player Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440006"
    And a player "a60e8400-e29b-41d4-a716-446655440006" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    And player "a60e8400-e29b-41d4-a716-446655440006" is in roster "960e8400-e29b-41d4-a716-446655440006"
    When I send a DELETE request to "/api/team/860e8400-e29b-41d4-a716-446655440006/roster/960e8400-e29b-41d4-a716-446655440006/player/a60e8400-e29b-41d4-a716-446655440006"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Remove player fails for non-creator/non-leader
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440007" exists with name "Protected Remove Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440007" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440007" exists for team "860e8400-e29b-41d4-a716-446655440007" with game "550e8400-e29b-41d4-a716-446655440080" and name "Protected Remove Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440007"
    And a player "a60e8400-e29b-41d4-a716-446655440007" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    And player "a60e8400-e29b-41d4-a716-446655440007" is in roster "960e8400-e29b-41d4-a716-446655440007"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/860e8400-e29b-41d4-a716-446655440007/roster/960e8400-e29b-41d4-a716-446655440007/player/a60e8400-e29b-41d4-a716-446655440007"
    Then the response status code should be 403

  Scenario: Successfully list players in a roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "860e8400-e29b-41d4-a716-446655440008" exists with name "List Players Team" created by "tester1@test.com"
    And team "860e8400-e29b-41d4-a716-446655440008" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "960e8400-e29b-41d4-a716-446655440008" exists for team "860e8400-e29b-41d4-a716-446655440008" with game "550e8400-e29b-41d4-a716-446655440080" and name "List Players Roster"
    And user "tester1@test.com" is a member of team "860e8400-e29b-41d4-a716-446655440008"
    And a player "a60e8400-e29b-41d4-a716-446655440008" exists for user "tester1@test.com" with game "550e8400-e29b-41d4-a716-446655440080"
    And player "a60e8400-e29b-41d4-a716-446655440008" is in roster "960e8400-e29b-41d4-a716-446655440008"
    When I send a GET request to "/api/team/860e8400-e29b-41d4-a716-446655440008/roster/960e8400-e29b-41d4-a716-446655440008/players"
    Then the response status code should be 200
    And the response should contain pagination structure
