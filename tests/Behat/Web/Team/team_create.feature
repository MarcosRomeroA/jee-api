@team @auth
Feature: Create Team
  In order to form gaming teams
  As an authenticated user
  I want to create a new team

  Scenario: Successfully create a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440070" with body:
      """
      {
        "name": "Pro Gamers Team",
        "description": "A professional gaming team"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with empty description
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440075" with body:
      """
      {
        "name": "Another Team",
        "description": ""
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team without optional fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440076" with body:
      """
      {
        "name": "Minimal Team"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create team with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/550e8400-e29b-41d4-a716-446655440071" with body:
      """
      {
        "description": "Missing name field"
      }
      """
    Then the response status code should be 422

  Scenario: Creator can update their own team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "750e8400-e29b-41d4-a716-446655440010" exists with name "Original Team Name" created by "tester1@test.com"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "name": "Updated Team Name",
        "description": "Updated description"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Leader can update the team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "750e8400-e29b-41d4-a716-446655440011" exists with name "Team With Leader" created by "tester1@test.com"
    And user "tester2@test.com" is the leader of team "750e8400-e29b-41d4-a716-446655440011"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "name": "Leader Updated Team",
        "description": "Updated by leader"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Non-creator and non-leader cannot update the team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    And a team "750e8400-e29b-41d4-a716-446655440012" exists with name "Protected Team" created by "tester1@test.com"
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440012" with body:
      """
      {
        "name": "Unauthorized Update",
        "description": "Should fail"
      }
      """
    Then the response status code should be 403
