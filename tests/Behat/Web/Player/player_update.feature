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
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440001"],
        "accountData": {
          "region": "las",
          "username": "OriginalRiot",
          "tag": "0001"
        }
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440200" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440002"],
        "accountData": {
          "region": "las",
          "username": "UpdatedRiot",
          "tag": "0002"
        }
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update player changes role and account data
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440201" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440081",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440005"],
        "accountData": {
          "region": "las",
          "username": "LoLUser",
          "tag": "1111"
        }
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440201" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440081",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440007"],
        "accountData": {
          "region": "las",
          "username": "LoLUserMid",
          "tag": "2222"
        }
      }
      """
    Then the response status code should be 200

  Scenario: Update player with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440202" with body:
      """
      {
        "accountData": {
          "region": "las",
          "username": "Test",
          "tag": "1234"
        }
      }
      """
    Then the response status code should be 422

  Scenario: Update player with non-existent game role
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440203" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["999e9999-e99b-99d9-a999-999999999999"],
        "accountData": {
          "region": "las",
          "username": "RiotPlayer",
          "tag": "1234"
        }
      }
      """
    Then the response status code should be 404

  Scenario: Update player with invalid accountData for Riot game
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440204" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440001"],
        "accountData": {
          "region": "las",
          "username": "ValidRiot",
          "tag": "1234"
        }
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440204" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440001"],
        "accountData": {}
      }
      """
    Then the response status code should be 422
