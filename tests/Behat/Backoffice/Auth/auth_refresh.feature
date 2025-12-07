@backoffice @auth
Feature: Refresh Admin Token
  In order to get a new access token
  As an authenticated admin
  I want to refresh my token using a refresh token

  Background:
    Given the following admins exist:
      | id                                   | name       | user      | password |
      | 750e8400-e29b-41d4-a716-446655440010 | Test Admin | testadmin | admin123 |

  Scenario: Successfully refresh token with valid refresh token
    Given I send a POST request to "/backoffice/login" with body:
      """
      {
        "user": "testadmin",
        "password": "admin123"
      }
      """
    Then the response status code should be 200
    And I save the "refreshToken" property as "savedRefreshToken"
    When I send a POST request to "/backoffice/refresh-token" with body:
      """
      {
        "refreshToken": "{savedRefreshToken}"
      }
      """
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "token" property
    And the response should have "refreshToken" property
    And the response should have "role" property

  Scenario: Refresh token with invalid refresh token
    Given I send a POST request to "/backoffice/refresh-token" with body:
      """
      {
        "refreshToken": "invalid.token.here"
      }
      """
    Then the response status code should be 401

  Scenario: Refresh token with access token instead of refresh token
    Given I send a POST request to "/backoffice/login" with body:
      """
      {
        "user": "testadmin",
        "password": "admin123"
      }
      """
    Then the response status code should be 200
    And I save the "token" property as "accessToken"
    When I send a POST request to "/backoffice/refresh-token" with body:
      """
      {
        "refreshToken": "{accessToken}"
      }
      """
    Then the response status code should be 401

  Scenario: Refresh token with missing refresh token
    Given I send a POST request to "/backoffice/refresh-token" with body:
      """
      {}
      """
    Then the response status code should be 422

  Scenario: Refresh token with user refresh token should fail
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 200
    And I save the "refreshToken" property as "userRefreshToken"
    When I send a POST request to "/backoffice/refresh-token" with body:
      """
      {
        "refreshToken": "{userRefreshToken}"
      }
      """
    Then the response status code should be 401
