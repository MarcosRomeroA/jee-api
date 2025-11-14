@post
Feature: Like and Dislike Post
  In order to interact with posts
  As an authenticated user
  I want to like or dislike posts

  Scenario: Successfully like a post
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/like" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Successfully dislike a post
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/dislike" with body:
      """
      {}
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Like a non-existent post
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/post/999e9999-e99b-99d9-a999-999999999999/like" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Dislike a non-existent post
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/post/999e9999-e99b-99d9-a999-999999999999/dislike" with body:
      """
      {}
      """
    Then the response status code should be 404
