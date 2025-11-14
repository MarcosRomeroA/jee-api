@auth
Feature: Refresh Token
  In order to get a new access token
  As an authenticated user
  I want to refresh my token using a refresh token

  Scenario: Successfully refresh token with valid refresh token
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "test@example.com",
        "password": "password123"
      }
      """
    Then the response status code should be 200
    And I save the "refreshToken" property as "savedRefreshToken"
    When I send a POST request to "/api/refresh-token" with body:
      """
      {
        "refreshToken": "{savedRefreshToken}"
      }
      """
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "token" property
    And the response should have "refreshToken" property
    And the response should have "notificationToken" property

  Scenario: Refresh token with invalid refresh token
    Given I send a POST request to "/api/refresh-token" with body:
      """
      {
        "refreshToken": "invalid.token.here"
      }
      """
    Then the response status code should be 401

  Scenario: Refresh token with access token instead of refresh token
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "test@example.com",
        "password": "password123"
      }
      """
    Then the response status code should be 200
    And I save the "token" property as "accessToken"
    When I send a POST request to "/api/refresh-token" with body:
      """
      {
        "refreshToken": "{accessToken}"
      }
      """
    Then the response status code should be 401

  Scenario: Refresh token with missing refresh token
    Given I send a POST request to "/api/refresh-token" with body:
      """
      {}
      """
    Then the response status code should be 422
