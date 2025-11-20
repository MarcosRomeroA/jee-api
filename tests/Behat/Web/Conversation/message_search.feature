@conversation
Feature: Search Messages
  In order to view conversation history
  As an authenticated user
  I want to retrieve messages from a conversation

  Scenario: Successfully retrieve messages from a conversation
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/messages"
    Then the response status code should be 200
    And the response should have "data" property as array

  Scenario: Retrieve messages with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/messages?page=1&limit=20"
    Then the response status code should be 200
    And the response should have "data" property as array

  Scenario: Retrieve messages from non-existent conversation
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversation/999e9999-e99b-99d9-a999-999999999999/messages"
    Then the response status code should be 500

  Scenario: Retrieve messages with invalid conversation id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversation/invalid-id/messages"
    Then the response status code should be 400
