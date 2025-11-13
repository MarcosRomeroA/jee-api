@conversation
Feature: Find Conversation by Other User
  In order to chat with a specific user
  As an authenticated user
  I want to find or create a conversation with another user

  Scenario: Successfully find conversation by other user id
    Given I send a GET request to "/api/conversation/by-other-user/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200

  Scenario: Find conversation with non-existent user
    Given I send a GET request to "/api/conversation/by-other-user/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find conversation with invalid user id format
    Given I send a GET request to "/api/conversation/by-other-user/invalid-id"
    Then the response status code should be 400

