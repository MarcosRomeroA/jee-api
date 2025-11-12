@player
Feature: Delete Player
  In order to remove a player from the system
  As an authenticated user
  I want to delete a player

  Scenario: Successfully delete a player
    Given I send a DELETE request to "/api/player/550e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete player with non-existent id
    Given I send a DELETE request to "/api/player/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Delete player with invalid id format
    Given I send a DELETE request to "/api/player/invalid-id"
    Then the response status code should be 400

