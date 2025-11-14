@user
Feature: Find User
  In order to view user profiles
  As a user
  I want to retrieve user information

  Scenario: Successfully find a user by id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "username" property
    And the response should have "email" property
    And the response should have "firstname" property
    And the response should have "lastname" property
    And the response should have "createdAt" property
    And the response should have "description" property

  Scenario: Find user by username
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/by-username/testuser"
    Then the response status code should be 200
    And the response should have "username" property

  Scenario: Find user with non-existent id
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find user with invalid id format
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/invalid-id"
    Then the response status code should be 400

  Scenario: Find user with non-existent username
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/user/by-username/nonexistentuser"
    Then the response status code should be 404
