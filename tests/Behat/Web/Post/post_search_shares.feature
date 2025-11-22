@post
Feature: Search Post Shares
  In order to view shares of a post
  As an authenticated user
  I want to retrieve shares for a post

  Scenario: Successfully search shares for a post
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/shares"
    Then the response status code should be 200

  Scenario: Search shares for non-existent post returns empty
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/999e9999-e99b-99d9-a999-999999999999/shares"
    Then the response status code should be 200

  Scenario: Search shares with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010/shares?limit=10&offset=0"
    Then the response status code should be 200
