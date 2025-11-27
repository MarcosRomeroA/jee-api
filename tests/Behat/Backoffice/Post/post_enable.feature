@backoffice @post
Feature: Enable post from backoffice
  As an admin
  I want to enable previously disabled posts
  So that content can be restored after review

  Scenario: Successfully enable a disabled post
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email            | password |
      | 850e8400-e29b-41d4-a716-446655440501 | John      | Doe      | johndoe501 | john501@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body                | disabled | moderation_reason     |
      | 950e8400-e29b-41d4-a716-446655440501 | 850e8400-e29b-41d4-a716-446655440501 | Previously disabled | true     | inappropriate_content |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440501/enable"
    Then the response status code should be 200

  Scenario: Enabled post should appear in web API search
    Given the following users exist:
      | id                                   | firstname | lastname | username | email            | password |
      | 850e8400-e29b-41d4-a716-446655440511 | Jane      | Smith    | jane511  | jane511@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body         | disabled | moderation_reason |
      | 950e8400-e29b-41d4-a716-446655440511 | 850e8400-e29b-41d4-a716-446655440511 | Was disabled | true     | spam              |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440511/enable"
    Then the response status code should be 200
    Given I am authenticated as "jane511@test.com" with password "pass123"
    When I send a GET request to "/api/posts?username=jane511"
    Then the response status code should be 200
    And the response metadata should have "count" property with value "1"

  Scenario: Non-admin cannot enable posts
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440521 | Alice     | Wonder   | alice521 | alice521@test.com | pass123  |
    And the following posts exist:
      | id                                   | user_id                              | body             | disabled | moderation_reason |
      | 950e8400-e29b-41d4-a716-446655440521 | 850e8400-e29b-41d4-a716-446655440521 | Disabled content | true     | harassment        |
    And I am authenticated as "alice521@test.com" with password "pass123"
    When I send a POST request to "/backoffice/post/950e8400-e29b-41d4-a716-446655440521/enable"
    Then the response status code should be 401

  Scenario: Enable non-existent post returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/post/999e8400-e29b-41d4-a716-446655440999/enable"
    Then the response status code should be 404
