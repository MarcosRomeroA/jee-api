@user
Feature: Search Users
  In order to find users in the system
  As a user
  I want to search for users with filters

  Scenario: Search all users without filters
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/users"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search users by query
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/users?q=test"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search users with pagination
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/users?page=1&limit=10"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And the response metadata should have "limit" property with value "10"

