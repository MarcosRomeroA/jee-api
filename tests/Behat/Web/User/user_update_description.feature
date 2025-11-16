@user
Feature: Update User Description
  In order to personalize my profile
  As an authenticated user
  I want to update my description

  Scenario: Successfully update user description
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PATCH request to "/api/user/description" with body:
      """
      {
        "description": "This is my new description"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update user description to empty string
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PATCH request to "/api/user/description" with body:
      """
      {
        "description": ""
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update user description without authentication
    When I send a PATCH request to "/api/user/description" with body:
      """
      {
        "description": "This should fail"
      }
      """
    Then the response status code should be 401

  Scenario: Update user description with invalid data type
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PATCH request to "/api/user/description" with body:
      """
      {
        "description": 123
      }
      """
    Then the response status code should be 422
