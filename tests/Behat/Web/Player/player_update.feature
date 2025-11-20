@player
Feature: Update Player
  In order to modify player information
  As an authenticated user
  I want to update a player profile

  Scenario: Successfully update an existing player
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440200" with body:
      """
      {
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440001",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440010",
        "username": "OriginalGamer"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440200" with body:
      """
      {
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440002",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440013",
        "username": "UpdatedGamer456"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update player changes role and rank
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440201" with body:
      """
      {
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440005",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440101",
        "username": "LoLPlayer"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440201" with body:
      """
      {
        "gameRoleId": "750e8400-e29b-41d4-a716-446655440007",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440120",
        "username": "LoLPlayerMid"
      }
      """
    Then the response status code should be 200

  Scenario: Update player with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440202" with body:
      """
      {
        "username": "UpdatedGamer456"
      }
      """
    Then the response status code should be 422

  Scenario: Update player with non-existent game role
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440203" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleId": "999e9999-e99b-99d9-a999-999999999999",
        "gameRankId": "850e8400-e29b-41d4-a716-446655440011",
        "username": "TestPlayer"
      }
      """
    Then the response status code should be 404
