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
