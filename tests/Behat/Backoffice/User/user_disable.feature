Feature: Disable user from backoffice
  As an admin
  I want to disable users
  So that they cannot access the platform

  @backoffice @user
  Scenario: Successfully disable a user
    Given the following users exist:
      | id                                   | firstname | lastname | username | email         | password |
      | 850e8400-e29b-41d4-a716-446655440001 | John      | Doe      | johndoe  | john@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440001/disable"
    Then the response status code should be 200

  @backoffice @user
  Scenario: Disabled user cannot login
    Given the following users exist:
      | id                                   | firstname | lastname | username  | email         | password |
      | 850e8400-e29b-41d4-a716-446655440002 | Jane      | Smith    | janesmith | jane@test.com | pass123  |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440002/disable"
    Then the response status code should be 200
    When I send a POST request to "/api/login" with body:
      """
      {
        "email": "jane@test.com",
        "password": "pass123"
      }
      """
    Then the response status code should be 401

  @backoffice @user
  Scenario: Non-admin cannot disable users
    Given the following users exist:
      | id                                   | firstname | lastname | username    | email            | password |
      | 850e8400-e29b-41d4-a716-446655440004 | Alice     | Wonder   | alicewonder | alice@test.com   | pass123  |
      | 850e8400-e29b-41d4-a716-446655440005 | Charlie   | Brown    | charlieb    | charlie@test.com | pass123  |
    And I am authenticated as "alice@test.com" with password "pass123"
    When I send a POST request to "/api/backoffice/user/850e8400-e29b-41d4-a716-446655440005/disable"
    Then the response status code should be 401

  @backoffice @user
  Scenario: Disable non-existent user returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/api/backoffice/user/999e8400-e29b-41d4-a716-446655440999/disable"
    Then the response status code should be 404
