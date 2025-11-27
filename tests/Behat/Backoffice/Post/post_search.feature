@backoffice @post
Feature: Search Posts from Backoffice
  In order to moderate content
  As an admin
  I want to search and filter posts

  Scenario: Successfully search all posts
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email            | password |
      | 850e8400-e29b-41d4-a716-446655440301 | John      | Doe      | johndoe301 | john301@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body                 |
      | 950e8400-e29b-41d4-a716-446655440301 | 850e8400-e29b-41d4-a716-446655440301 | Test post content    |
      | 950e8400-e29b-41d4-a716-446655440302 | 850e8400-e29b-41d4-a716-446655440301 | Another post content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/posts"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search posts by user email
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440311 | Alice     | Johnson  | alice311 | alice311@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440312 | Bob       | Williams | bob312   | bob312@test.com   | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body       |
      | 950e8400-e29b-41d4-a716-446655440311 | 850e8400-e29b-41d4-a716-446655440311 | Alice post |
      | 950e8400-e29b-41d4-a716-446655440312 | 850e8400-e29b-41d4-a716-446655440312 | Bob post   |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/posts?email=alice311"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search posts by username
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440321 | Charlie   | Brown    | charlie321 | charlie321@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body         |
      | 950e8400-e29b-41d4-a716-446655440321 | 850e8400-e29b-41d4-a716-446655440321 | Charlie post |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/posts?username=charlie321"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search posts by post ID
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440331 | David     | Miller   | david331 | david331@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body       |
      | 950e8400-e29b-41d4-a716-446655440331 | 850e8400-e29b-41d4-a716-446655440331 | David post |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/posts?postId=950e8400-e29b-41d4-a716-446655440331"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search without authentication should fail
    When I send a GET request to "/backoffice/posts"
    Then the response status code should be 401
