@tournament
Feature: Search Tournament Status
  In order to know available tournament statuses
  As an authenticated user
  I want to retrieve all tournament statuses

  Scenario: Successfully retrieve all tournament statuses
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament-status"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
