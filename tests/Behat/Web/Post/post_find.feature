@post
Feature: Find Post
  In order to view post details
  As an authenticated user
  I want to retrieve a post by id

  Scenario: Successfully find a post by id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/550e8400-e29b-41d4-a716-446655440010"
    Then the response status code should be 200
    And the response should have property "id" with value "550e8400-e29b-41d4-a716-446655440010"
    And the response should have property "body"
    And the response should have property "username"
    And the response should have property "createdAt"

  Scenario: Find post with non-existent id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: Find post with invalid id format
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/post/invalid-id"
    Then the response status code should be 400
