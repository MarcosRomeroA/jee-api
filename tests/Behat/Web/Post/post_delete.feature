@post
Feature: Delete Post
  In order to remove unwanted content
  As an authenticated user
  I want to delete my posts

  Scenario: Successfully delete a post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/post/550e8400-e29b-41d4-a716-446655440010/delete"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete non-existent post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/post/999e9999-e99b-99d9-a999-999999999999/delete"
    Then the response status code should be 404

  Scenario: Delete post with invalid id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/post/invalid-id/delete"
    Then the response status code should be 400
