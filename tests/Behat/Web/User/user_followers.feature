@user
Feature: Get User Followers
  In order to see who follows a user
  As a user
  I want to retrieve the list of followers

  Scenario: Successfully get followers list
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followers"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Get followers with pagination
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followers?page=1&limit=10"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And the response metadata should have "limit" property with value "10"

  Scenario: Get followers of non-existent user
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/999e9999-e99b-99d9-a999-999999999999/followers"
    Then the response status code should be 422

  Scenario: Get followers with invalid user id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/invalid-id/followers"
    Then the response status code should be 422
