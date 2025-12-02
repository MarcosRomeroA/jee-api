@player
Feature: Create Player
  In order to register a player in the system
  As an authenticated user
  I want to create a new player profile

  Scenario: Successfully create a player for Valorant (Riot game)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440100" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440001"],
        "accountData": {
          "region": "las",
          "username": "RiotPlayer",
          "tag": "1234"
        }
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully create a player for CS2 (Steam game)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440105" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440082",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440010"],
        "accountData": {
          "steamId": "76561198012345678"
        }
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully create a player for CS2 with Steam profile URL
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440108" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440082",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440010"],
        "accountData": {
          "steamId": "https://steamcommunity.com/profiles/76561198012345679"
        }
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update existing player (UPSERT behavior)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440101" with body:
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
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440101" with body:
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

  Scenario: Successfully create a player with empty roles
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440109" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": [],
        "accountData": {
          "region": "las",
          "username": "NoRolesPlayer",
          "tag": "1234"
        }
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create player with missing required fields
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440102" with body:
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

  Scenario: Create player with non-existent game role
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440103" with body:
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

  Scenario: Create player with missing accountData for Riot game
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440106" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440001"],
        "accountData": {}
      }
      """
    Then the response status code should be 422

  Scenario: Create player with missing accountData for Steam game
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440107" with body:
      """
      {
        "gameId": "550e8400-e29b-41d4-a716-446655440082",
        "gameRoleIds": ["750e8400-e29b-41d4-a716-446655440010"],
        "accountData": {}
      }
      """
    Then the response status code should be 422
