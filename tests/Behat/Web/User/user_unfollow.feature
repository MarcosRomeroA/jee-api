@user
Feature: Unfollow User
  In order to stop receiving updates from a user
  As an authenticated user
  I want to unfollow users

  Scenario: Successfully unfollow a user
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/unfollow" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Unfollow non-existent user
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/999e9999-e99b-99d9-a999-999999999999/unfollow" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Unfollow user with invalid id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/invalid-id/unfollow" with body:
      """
      {}
      """
    Then the response status code should be 400
