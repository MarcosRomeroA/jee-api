@post
Feature: Search Post Likes
  In order to view likes on a post
  As an authenticated user
  I want to retrieve likes for a post

  Scenario: Successfully search likes for a post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/likes"
    Then the response status code should be 200

  Scenario: Search likes for non-existent post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/999e9999-e99b-99d9-a999-999999999999/likes"
    Then the response status code should be 404

  Scenario: Search likes with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/likes?limit=10&offset=0"
    Then the response status code should be 200
