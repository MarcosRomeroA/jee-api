@post
Feature: Add Comment to Post
  In order to interact with posts
  As an authenticated user
  I want to add comments to posts

  Scenario: Successfully add a comment to a post
    Given I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comment" with body:
      """
      {
        "id": "550e8400-e29b-41d4-a716-446655440020",
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "Great post! I totally agree."
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Add comment with empty content
    Given I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010/comment" with body:
      """
      {
        "id": "550e8400-e29b-41d4-a716-446655440021",
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": ""
      }
      """
    Then the response status code should be 400

  Scenario: Add comment to non-existent post
    Given I send a PUT request to "/api/post/999e9999-e99b-99d9-a999-999999999999/comment" with body:
      """
      {
        "id": "550e8400-e29b-41d4-a716-446655440022",
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "This is a comment"
      }
      """
    Then the response status code should be 404

