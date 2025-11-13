@post
Feature: Search Post Comments
  In order to view comments on a post
  As an authenticated user
  I want to retrieve comments for a post

  Scenario: Successfully search comments for a post
    Given I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comments"
    Then the response status code should be 200

  Scenario: Search comments for non-existent post
    Given I send a GET request to "/api/post/999e9999-e99b-99d9-a999-999999999999/comments"
    Then the response status code should be 404

  Scenario: Search comments with pagination
    Given I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comments?page=1&limit=10"
    Then the response status code should be 200

