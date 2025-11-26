@backoffice @user
Feature: Search Users
  In order to manage users
  As an admin
  I want to search and filter users

  Scenario: Successfully search all users
    Given the following users exist:
      | id                                   | firstname | lastname | username     | email            | password |
      | 850e8400-e29b-41d4-a716-446655440201 | John      | Doe      | johndoe201   | john201@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440202 | Jane      | Smith    | janesmith202 | jane202@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search users by email
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440211 | Alice     | Johnson  | alice211 | alice211@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440212 | Bob       | Williams | bob212   | bob212@test.com   | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users?email=alice211"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search users by username
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440221 | Charlie   | Brown    | charlie221 | charlie221@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440222 | David     | Miller   | david222   | david222@test.com   | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users?username=charlie"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search verified users only
    Given the following users exist:
      | id                                   | firstname  | lastname | username      | email                  | password |
      | 850e8400-e29b-41d4-a716-446655440231 | Verified   | User     | verified231   | verified231@test.com   | pass123  |
      | 850e8400-e29b-41d4-a716-446655440232 | Unverified | User     | unverified232 | unverified232@test.com | pass123  |
    And the following email confirmations exist:
      | user_id                              | token    | confirmed_at |
      | 850e8400-e29b-41d4-a716-446655440232 | token232 | null         |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users?verified=true"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search unverified users only
    Given the following users exist:
      | id                                   | firstname  | lastname | username      | email                  | password |
      | 850e8400-e29b-41d4-a716-446655440241 | Verified   | User     | verified241   | verified241@test.com   | pass123  |
      | 850e8400-e29b-41d4-a716-446655440242 | Unverified | User     | unverified242 | unverified242@test.com | pass123  |
    And the following email confirmations exist:
      | user_id                              | token    | confirmed_at |
      | 850e8400-e29b-41d4-a716-446655440242 | token242 | null         |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users?verified=false"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search users with pagination
    Given the following users exist:
      | id                                   | firstname | lastname | username | email            | password |
      | 850e8400-e29b-41d4-a716-446655440251 | User      | One      | user251  | user251@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440252 | User      | Two      | user252  | user252@test.com | pass123  |
      | 850e8400-e29b-41d4-a716-446655440253 | User      | Three    | user253  | user253@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/api/backoffice/users?limit=2&offset=0"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "limit" property with value "2"
    And the response metadata should have "offset" property with value "0"

  Scenario: Search without authentication should fail
    When I send a GET request to "/api/backoffice/users"
    Then the response status code should be 401
