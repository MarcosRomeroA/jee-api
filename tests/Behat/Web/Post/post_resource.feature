@post
Feature: Post Resource Management
  In order to add media content to posts
  As an authenticated user
  I want to create posts with resources in a single request

  Scenario: Successfully create a post with an image resource
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a "POST" request to "/api/post/650e8400-e29b-41d4-a716-446655440020" with file "files[0]" and parameters:
      """
      {
        "body": "Check out this image!"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create post without resources using JSON
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a POST request to "/api/post/650e8400-e29b-41d4-a716-446655440021" with body:
      """
      {
        "body": "A simple text post without images"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create post without authentication should fail
    Given I am not authenticated
    When I send a "POST" request to "/api/post/650e8400-e29b-41d4-a716-446655440022" with file "files[0]" and parameters:
      """
      {
        "body": "This should fail"
      }
      """
    Then the response status code should be 401
