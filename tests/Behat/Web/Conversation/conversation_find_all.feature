@conversation
Feature: Find Conversations
  In order to view my conversations
  As an authenticated user
  I want to retrieve all my active conversations

  Scenario: Successfully retrieve all conversations
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversations"
    Then the response status code should be 200

  Scenario: Retrieve conversations with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/conversations?page=1&limit=10"
    Then the response status code should be 200
