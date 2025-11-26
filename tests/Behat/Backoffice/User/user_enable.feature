Feature: Enable user from backoffice
  As an admin
  I want to enable disabled users
  So that they can access the platform again

  @backoffice @user
  Scenario: Successfully enable a disabled user
    Given the following users exist:
      | id                                   | firstname | lastname | username  | email         | password |
      | 850e8400-e29b-41d4-a716-446655440010 | John      | Doe      | johndoe10 | john@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440010/disable"
    Then the response status code should be 200
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440010/enable"
    Then the response status code should be 200

  @backoffice @user
  Scenario: Enabled user can login again
    Given the following users exist:
      | id                                   | firstname | lastname | username    | email         | password |
      | 850e8400-e29b-41d4-a716-446655440011 | Jane      | Smith    | janesmith11 | jane@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440011/disable"
    Then the response status code should be 200
    When I send a POST request to "/api/login" with body:
      """
      {
        "email": "jane@test.com",
        "password": "pass123"
      }
      """
    Then the response status code should be 401
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440011/enable"
    Then the response status code should be 200
    When I send a POST request to "/api/login" with body:
      """
      {
        "email": "jane@test.com",
        "password": "pass123"
      }
      """
    Then the response status code should be 200

  @backoffice @user
  Scenario: Enabled user can access protected endpoints
    Given the following users exist:
      | id                                   | firstname | lastname | username     | email        | password |
      | 850e8400-e29b-41d4-a716-446655440012 | Bob       | Johnson  | bobjohnson12 | bob@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440012/disable"
    Then the response status code should be 200
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440012/enable"
    Then the response status code should be 200
    Given I am authenticated as "bob@test.com" with password "pass123"
    When I send a GET request to "/api/user/850e8400-e29b-41d4-a716-446655440012"
    Then the response status code should be 200

  @backoffice @user
  Scenario: Non-admin cannot enable users
    Given the following users exist:
      | id                                   | firstname | lastname | username      | email            | password |
      | 850e8400-e29b-41d4-a716-446655440013 | Alice     | Wonder   | alicewonder13 | alice@test.com   | pass123  |
      | 850e8400-e29b-41d4-a716-446655440014 | Charlie   | Brown    | charlieb14    | charlie@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440014/disable"
    Then the response status code should be 200
    Given I am authenticated as "alice@test.com" with password "pass123"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440014/enable"
    Then the response status code should be 401

  @backoffice @user
  Scenario: Enable non-existent user returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/999e8400-e29b-41d4-a716-446655440999/enable"
    Then the response status code should be 404
