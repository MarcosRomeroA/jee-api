@backoffice @post
Feature: Disable post from backoffice
  As an admin
  I want to disable posts
  So that inappropriate content is not shown to users

  Scenario: Successfully disable a post for inappropriate content
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email            | password |
      | 850e8400-e29b-41d4-a716-446655440401 | John      | Doe      | johndoe401 | john401@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body                  |
      | 950e8400-e29b-41d4-a716-446655440401 | 850e8400-e29b-41d4-a716-446655440401 | Inappropriate content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440401/disable" with body:
      """
      {
        "reason": "inappropriate_content"
      }
      """
    Then the response status code should be 200

  Scenario: Disable post for spam
    Given the following users exist:
      | id                                   | firstname | lastname | username | email            | password |
      | 850e8400-e29b-41d4-a716-446655440411 | Jane      | Smith    | jane411  | jane411@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body                  |
      | 950e8400-e29b-41d4-a716-446655440411 | 850e8400-e29b-41d4-a716-446655440411 | Buy now! Spam content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440411/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 200

  Scenario: Disable post for harassment
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440421 | Alice     | Wonder   | alice421 | alice421@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body               |
      | 950e8400-e29b-41d4-a716-446655440421 | 850e8400-e29b-41d4-a716-446655440421 | Harassment content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440421/disable" with body:
      """
      {
        "reason": "harassment"
      }
      """
    Then the response status code should be 200

  Scenario: Disabled post should not appear in web API search
    Given the following users exist:
      | id                                   | firstname | lastname | username | email           | password |
      | 850e8400-e29b-41d4-a716-446655440431 | Bob       | Builder  | bob431   | bob431@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body          | disabled | moderation_reason     |
      | 950e8400-e29b-41d4-a716-446655440431 | 850e8400-e29b-41d4-a716-446655440431 | Disabled post | true     | inappropriate_content |
      | 950e8400-e29b-41d4-a716-446655440432 | 850e8400-e29b-41d4-a716-446655440431 | Enabled post  | false    |                       |
    And I am authenticated as "bob431@test.com" with password "pass123"
    When I send a GET request to "/api/posts?username=bob431"
    Then the response status code should be 200
    And the response metadata should have "count" property with value "1"

  Scenario: Disable with invalid reason should fail
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440441 | Charlie   | Brown    | charlie441 | charlie441@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body         |
      | 950e8400-e29b-41d4-a716-446655440441 | 850e8400-e29b-41d4-a716-446655440441 | Some content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440441/disable" with body:
      """
      {
        "reason": "invalid_reason"
      }
      """
    Then the response status code should be 422

  Scenario: Non-admin cannot disable posts
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440451 | David     | Miller   | david451 | david451@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body         |
      | 950e8400-e29b-41d4-a716-446655440451 | 850e8400-e29b-41d4-a716-446655440451 | Some content |
    And I am authenticated as "david451@test.com" with password "pass123"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440451/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 401

  Scenario: Disable non-existent post returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/999e8400-e29b-41d4-a716-446655440999/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 404
