@notification
Feature: Search Notifications
  In order to stay informed about system events
  As an authenticated user
  I want to retrieve my notifications

  Scenario: Successfully retrieve all notifications
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/notifications"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Retrieve notifications with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/notifications?page=1&limit=10"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Retrieve only unread notifications
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/notifications?unread=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
