@team @auth @roster
Feature: Roster Management
  In order to organize team players for competitions
  As a team creator or leader
  I want to manage rosters for my team

  Scenario: Successfully create a roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440001" exists with name "Roster Test Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440001" has game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440001/roster/950e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Main Roster",
        "description": "Our main competitive roster"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create roster without optional description
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440002" exists with name "Roster Test Team 2" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440002" has game "550e8400-e29b-41d4-a716-446655440080"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440002/roster/950e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Secondary Roster"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create roster fails when game is not associated to team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440003" exists with name "No Game Team" created by "tester1@test.com"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440003/roster/950e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Roster Without Game"
      }
      """
    Then the response status code should be 409

  Scenario: Create roster fails for non-creator/non-leader
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440004" exists with name "Other User Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440004" has game "550e8400-e29b-41d4-a716-446655440080"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440004/roster/950e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Unauthorized Roster"
      }
      """
    Then the response status code should be 403

  Scenario: Leader can create roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440005" exists with name "Leader Roster Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440005" has game "550e8400-e29b-41d4-a716-446655440080"
    And user "tester2@test.com" is the leader of team "850e8400-e29b-41d4-a716-446655440005"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440005/roster/950e8400-e29b-41d4-a716-446655440005" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Leader Created Roster"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully update a roster
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440006" exists with name "Update Roster Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440006" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "950e8400-e29b-41d4-a716-446655440006" exists for team "850e8400-e29b-41d4-a716-446655440006" with game "550e8400-e29b-41d4-a716-446655440080" and name "Original Roster"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440006/roster/950e8400-e29b-41d4-a716-446655440006" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "name": "Updated Roster Name",
        "description": "Updated description"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully delete a roster (creator only)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440007" exists with name "Delete Roster Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440007" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "950e8400-e29b-41d4-a716-446655440007" exists for team "850e8400-e29b-41d4-a716-446655440007" with game "550e8400-e29b-41d4-a716-446655440080" and name "Roster To Delete"
    When I send a DELETE request to "/api/team/850e8400-e29b-41d4-a716-446655440007/roster/950e8400-e29b-41d4-a716-446655440007"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete roster fails for leader (only creator can delete)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440008" exists with name "Leader Delete Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440008" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "950e8400-e29b-41d4-a716-446655440008" exists for team "850e8400-e29b-41d4-a716-446655440008" with game "550e8400-e29b-41d4-a716-446655440080" and name "Protected Roster"
    And user "tester2@test.com" is the leader of team "850e8400-e29b-41d4-a716-446655440008"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/850e8400-e29b-41d4-a716-446655440008/roster/950e8400-e29b-41d4-a716-446655440008"
    Then the response status code should be 403

  Scenario: Successfully list rosters for a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "850e8400-e29b-41d4-a716-446655440009" exists with name "List Rosters Team" created by "tester1@test.com"
    And team "850e8400-e29b-41d4-a716-446655440009" has game "550e8400-e29b-41d4-a716-446655440080"
    And a roster "950e8400-e29b-41d4-a716-446655440009" exists for team "850e8400-e29b-41d4-a716-446655440009" with game "550e8400-e29b-41d4-a716-446655440080" and name "First Roster"
    And a roster "950e8400-e29b-41d4-a716-446655440010" exists for team "850e8400-e29b-41d4-a716-446655440009" with game "550e8400-e29b-41d4-a716-446655440080" and name "Second Roster"
    When I send a GET request to "/api/team/850e8400-e29b-41d4-a716-446655440009/rosters"
    Then the response status code should be 200
    And the response should contain pagination structure
