@conversation
Feature: Create Message
  In order to communicate with other users
  As an authenticated user
  I want to send messages in a conversation

  Scenario: Successfully create a message in a conversation
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440060" with body:
      """
      {
        "content": "Hello! How are you doing?"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create message with empty content
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440051" with body:
      """
      {
        "content": ""
      }
      """
    Then the response status code should be 422

  Scenario: Create message in non-existent conversation
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/conversation/999e9999-e99b-99d9-a999-999999999999/message/550e8400-e29b-41d4-a716-446655440052" with body:
      """
      {
        "content": "This is a test message"
      }
      """
    Then the response status code should be 404

  Scenario: Create message with invalid conversation id format
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/conversation/invalid-id/message/550e8400-e29b-41d4-a716-446655440053" with body:
      """
      {
        "content": "This is a test message"
      }
      """
    Then the response status code should be 400
