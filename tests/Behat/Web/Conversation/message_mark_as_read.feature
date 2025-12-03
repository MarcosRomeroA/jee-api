@conversation
Feature: Mark Message as Read
  In order to track message read status
  As an authenticated user
  I want to mark messages as read in a conversation

  Scenario: Successfully mark a message as read
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # First create a message from user2 that user1 will mark as read
    And I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440070" with body:
      """
      {
        "content": "Message to be marked as read"
      }
      """
    Then the response status code should be 200
    # Now user1 marks the message as read
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/550e8400-e29b-41d4-a716-446655440070/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Mark message as read in non-existent conversation
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/999e9999-e99b-99d9-a999-999999999999/message/550e8400-e29b-41d4-a716-446655440071/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Mark non-existent message as read
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/550e8400-e29b-41d4-a716-446655440040/message/999e9999-e99b-99d9-a999-999999999999/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Mark message as read with invalid conversation id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/conversation/invalid-id/message/550e8400-e29b-41d4-a716-446655440072/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 400
