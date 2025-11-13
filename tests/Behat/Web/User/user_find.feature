@user
Feature: Find User
  In order to view user profiles
  As a user
  I want to retrieve user information

  Scenario: Successfully find a user by id
    Given I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should have property "id" with value "550e8400-e29b-41d4-a716-446655440001"
    And the response should have property "username"
    And the response should have property "email"
    And the response should have property "firstname"
    And the response should have property "lastname"
    And the response should have property "createdAt"
    And the response should have property "description"
    And the response should have property "teams"
    And the response should have property "tournaments"

  Scenario: Find user by username
    Given I send a GET request to "/api/user/username/testuser"
    Then the response status code should be 200
    And the response should have property "username" with value "testuser"
    And the response should have property "teams"
    And the response should have property "tournaments"

  Scenario: Find user with non-existent id
    Given I send a GET request to "/api/user/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find user with invalid id format
    Given I send a GET request to "/api/user/invalid-id"
    Then the response status code should be 400

  Scenario: Find user with non-existent username
    Given I send a GET request to "/api/user/username/nonexistentuser"
    Then the response status code should be 404

