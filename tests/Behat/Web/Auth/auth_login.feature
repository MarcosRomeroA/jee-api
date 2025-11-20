@auth
Feature: Login User
  In order to authenticate in the system
  As a user
  I want to login with my credentials

  Scenario: Successfully login with valid credentials
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "token" property
    And the response should have "refreshToken" property
    And the response should have "notificationToken" property

  Scenario: Login with invalid email
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "invalid@example.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 401

  Scenario: Login with invalid password
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com",
        "password": "wrongpassword"
      }
      """
    Then the response status code should be 401

  Scenario: Login with missing email
    Given I send a POST request to "/api/login" with body:
      """
      {
        "password": "12345678"
      }
      """
    Then the response status code should be 422

  Scenario: Login with missing password
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com"
      }
      """
    Then the response status code should be 422

  Scenario: Login with invalid email format
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "invalid-email",
        "password": "12345678"
      }
      """
    Then the response status code should be 422

  Scenario: Login with unverified email should fail
    Given the following users exist:
      | id                                   | firstname  | lastname | username     | email               | password |
      | 650e8400-e29b-41d4-a716-446655440099 | Unverified | User     | unverified99 | unverified@test.com | 12345678 |
    When I send a POST request to "/api/login" with body:
      """
      {
        "email": "unverified@test.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 403
