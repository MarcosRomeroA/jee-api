@backoffice @auth
Feature: Login Admin
  In order to authenticate in the backoffice
  As an admin
  I want to login with my credentials

  Background:
    Given the following admins exist:
      | id                                   | name       | user      | password |
      | 750e8400-e29b-41d4-a716-446655440010 | Test Admin | testadmin | admin123 |

  Scenario: Successfully login with valid credentials
    Given I send a POST request to "/api/backoffice/login" with body:
      """
      {
        "user": "testadmin",
        "password": "admin123"
      }
      """
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "token" property
    And the response should have "refreshToken" property
    And the response should have "role" property

  Scenario: Login with invalid user
    Given I send a POST request to "/api/backoffice/login" with body:
      """
      {
        "user": "invaliduser",
        "password": "admin123"
      }
      """
    Then the response status code should be 401

  Scenario: Login with invalid password
    Given I send a POST request to "/api/backoffice/login" with body:
      """
      {
        "user": "testadmin",
        "password": "wrongpassword"
      }
      """
    Then the response status code should be 401

  Scenario: Login with missing user
    Given I send a POST request to "/api/backoffice/login" with body:
      """
      {
        "password": "admin123"
      }
      """
    Then the response status code should be 422

  Scenario: Login with missing password
    Given I send a POST request to "/api/backoffice/login" with body:
      """
      {
        "user": "testadmin"
      }
      """
    Then the response status code should be 422
