@notification
Feature: Mark Notification as Read
  In order to manage my notifications
  As an authenticated user
  I want to mark notifications as read

  Scenario: Successfully mark a notification as read
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/notification/550e8400-e29b-41d4-a716-446655440036/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Mark non-existent notification as read
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/notification/999e9999-e99b-99d9-a999-999999999999/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Mark notification as read with invalid id format
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/notification/invalid-id/mark-as-read" with body:
      """
      {}
      """
    Then the response status code should be 400
