@post
Feature: Search Posts
  In order to browse posts in the system
  As an authenticated user
  I want to search for posts

  Scenario: Search all posts without filters
    Given I send a GET request to "/api/posts"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search posts by query
    Given I send a GET request to "/api/posts?q=gaming"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search posts with pagination
    Given I send a GET request to "/api/posts?page=1&limit=10"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

