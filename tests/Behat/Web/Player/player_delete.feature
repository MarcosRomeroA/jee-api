@player
Feature: Delete Player
  In order to remove a player from the system
  As an authenticated user
  I want to delete a player

  Scenario: Successfully delete a player
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/player/550e8400-e29b-41d4-a716-446655440400" with body:
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
    When I send a DELETE request to "/api/player/550e8400-e29b-41d4-a716-446655440400"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete player with non-existent id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/player/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Delete player with invalid id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/player/invalid-id"
    Then the response status code should be 400
