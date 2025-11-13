@conversation
Feature: Create Message
  In order to communicate with other users
  As an authenticated user
  I want to send messages in a conversation

  Scenario: Successfully create a message in a conversation
    Given I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440050" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "Hello! How are you doing?"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create message with empty content
    Given I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440051" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": ""
      }
      """
    Then the response status code should be 400

  Scenario: Create message in non-existent conversation
    Given I send a PUT request to "/api/conversation/999e9999-e99b-99d9-a999-999999999999/message/550e8400-e29b-41d4-a716-446655440052" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "This is a test message"
      }
      """
    Then the response status code should be 404

  Scenario: Create message with invalid conversation id format
    Given I send a PUT request to "/api/conversation/invalid-id/message/550e8400-e29b-41d4-a716-446655440053" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "This is a test message"
      }
      """
    Then the response status code should be 400

