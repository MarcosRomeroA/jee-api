@post
Feature: Search Posts
  In order to browse posts in the system
  As an authenticated user
  I want to search for posts

  Scenario: Search all posts without filters
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search posts by query matching body content
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?q=gaming"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And all posts in response should have body or username containing "gaming"

  Scenario: Search posts by query matching username
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?q=tester1"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And all posts in response should have body or username containing "tester1"

  Scenario: Search posts by query with no results
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?q=xyznonexistent123"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as empty array

  Scenario: Search posts with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?page=1&limit=10"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array

  Scenario: Search posts by username filter with exact match
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?username=tester1"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And all posts in response should have username containing "tester1"

  Scenario: Search posts by username filter with partial match
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?username=tester"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And all posts in response should have username containing "tester"

  Scenario: Search posts by non-existent username returns empty
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?username=nonexistentuser123"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as empty array

  Scenario: Search posts by userId filter
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?userId=550e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as array
    And all posts in response should belong to username "tester1"

  Scenario: Search posts by non-existent userId returns empty
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/posts?userId=999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response should have "data" property as empty array
