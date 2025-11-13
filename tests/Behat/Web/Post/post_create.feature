@post
Feature: Create Post
  In order to share content in the system
  As an authenticated user
  I want to create a new post

  Scenario: Successfully create a post
    Given I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": "This is my first post about gaming!"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create post with empty content
    Given I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "userId": "550e8400-e29b-41d4-a716-446655440001",
        "content": ""
      }
      """
    Then the response status code should be 400

  Scenario: Create post with invalid user id
    Given I send a PUT request to "/api/post/550e8400-e29b-41d4-a716-446655440012" with body:
      """
      {
        "userId": "invalid-uuid",
        "content": "This is a test post"
      }
      """
    Then the response status code should be 400

