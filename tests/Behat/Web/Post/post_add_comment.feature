@post
Feature: Add Comment to Post
  In order to interact with posts
  As an authenticated user
  I want to add comments to posts

  Scenario: Successfully add a comment to a post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comment" with body:
      """
      {
        "commentId": "550e8400-e29b-41d4-a716-446655440020",
        "commentBody": "Great post! I totally agree."
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Add comment with empty content
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comment" with body:
      """
      {
        "commentId": "550e8400-e29b-41d4-a716-446655440021",
        "commentBody": ""
      }
      """
    Then the response status code should be 200

  Scenario: Add comment to non-existent post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/post/999e9999-e99b-99d9-a999-999999999999/comment" with body:
      """
      {
        "commentId": "550e8400-e29b-41d4-a716-446655440022",
        "commentBody": "This is a comment"
      }
      """
    Then the response status code should be 404
