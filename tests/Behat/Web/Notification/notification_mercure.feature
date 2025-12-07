@notification @mercure
Feature: Mercure Notification Real-time
  In order to receive real-time notifications
  As an authenticated user
  I want to verify that notifications are published to Mercure

  Background:
    Given I am authenticated as "tester1@test.com" with password "12345678"

  Scenario: Verify Mercure configuration is present
    Then I should be able to subscribe to Mercure notifications

  Scenario: Verify notification event subscriber executes without errors
    When I send a GET request to "/api/notifications"
    Then the response status code should be 200
    And the Mercure notification should be published

  @realtime @post
  Scenario: Receive real-time notification when someone likes my post (POST_LIKED)
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440001"
    And I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/like" with body:
      """
      {}
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "post_liked"

  @realtime @post
  Scenario: Receive real-time notification when someone comments my post (POST_COMMENTED)
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440001"
    And I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comment" with body:
      """
      {
        "commentId": "850e8400-e29b-41d4-a716-446655440998",
        "commentBody": "Great post! I really enjoyed reading this."
      }
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "post_commented"

  @realtime @user
  Scenario: Receive real-time notification when someone follows me (NEW_FOLLOWER)
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440001"
    And I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440001/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "new_follower"

  @realtime @conversation
  Scenario: Receive real-time notification when someone sends me a message (NEW_MESSAGE)
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440001"
    And I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/950e8400-e29b-41d4-a716-446655440999" with body:
      """
      {
        "content": "Hello! How are you?"
      }
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "new_message"

  @realtime @team
  Scenario: Receive real-time notification when team request is accepted (TEAM_REQUEST_ACCEPTED)
    # tester1 creates a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Notification Test Team",
        "description": "Team for testing acceptance notifications"
      }
      """
    Then the response status code should be 200
    # tester2 requests to join
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440001/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 gets the request ID
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=950e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    # tester2 listens to Mercure and tester1 accepts
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440002"
    And I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "team_request_accepted"

  @realtime @tournament
  Scenario: Receive real-time notification when tournament request is accepted (TOURNAMENT_REQUEST_ACCEPTED)
    # tester1 creates a tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/tournament/950e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Notification Test Tournament",
        "description": "Tournament for testing acceptance notifications",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 8,
        "startAt": "2026-06-15T10:00:00Z",
        "endAt": "2026-06-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # tester1 creates a team
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Tournament Request Team"
      }
      """
    Then the response status code should be 200
    # tester1 requests to join the tournament with the team
    When I send a PUT request to "/api/tournament/950e8400-e29b-41d4-a716-446655440002/team/950e8400-e29b-41d4-a716-446655440003/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get the request ID
    When I send a GET request to "/api/tournament/requests?tournamentId=950e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    # tester1 listens to Mercure (as team creator) and accepts the tournament request
    Given I am listening to Mercure notifications for user "550e8400-e29b-41d4-a716-446655440001"
    When I send a PUT request to "/api/tournament/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    And I should receive a Mercure notification about "tournament_request_accepted"
