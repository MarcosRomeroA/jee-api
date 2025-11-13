@auth
Feature: Login User
  In order to authenticate in the system
  As a user
  I want to login with my credentials

  Scenario: Successfully login with valid credentials
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "email": "test@example.com",
      "password": "password123"
    }
    """
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "token" property
    And the response should have "refreshToken" property
    And the response should have "notificationToken" property

  Scenario: Login with invalid email
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "email": "invalid@example.com",
      "password": "password123"
    }
    """
    Then the response status code should be 401

  Scenario: Login with invalid password
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "email": "test@example.com",
      "password": "wrongpassword"
    }
    """
    Then the response status code should be 401

  Scenario: Login with missing email
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "password": "password123"
    }
    """
    Then the response status code should be 422

  Scenario: Login with missing password
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "email": "test@example.com"
    }
    """
    Then the response status code should be 422

  Scenario: Login with invalid email format
    Given I send a POST request to "/api/auth/login" with body:
    """
    {
      "email": "invalid-email",
      "password": "password123"
    }
    """
    Then the response status code should be 422

