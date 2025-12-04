@backoffice @team
Feature: Disable team from backoffice
  As an admin
  I want to disable teams
  So that inappropriate teams are not shown to users

  Scenario: Successfully disable a team for inappropriate content
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email            | password |
      | 850e8400-e29b-41d4-a716-446655440501 | John      | Doe      | johndoe501 | john501@test.com | pass123  |
    And the following teams exist:
      | id                                   | name            | creator_id                           |
      | 750e8400-e29b-41d4-a716-446655440501 | Team to disable | 850e8400-e29b-41d4-a716-446655440501 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/team/750e8400-e29b-41d4-a716-446655440501/disable" with body:
      """
      {
        "reason": "inappropriate_content"
      }
      """
    Then the response status code should be 200

  Scenario: Disable team for spam
    Given the following users exist:
      | id                                   | firstname | lastname | username | email            | password |
      | 850e8400-e29b-41d4-a716-446655440511 | Jane      | Smith    | jane511  | jane511@test.com | pass123  |
    And the following teams exist:
      | id                                   | name      | creator_id                           |
      | 750e8400-e29b-41d4-a716-446655440511 | Spam Team | 850e8400-e29b-41d4-a716-446655440511 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/team/750e8400-e29b-41d4-a716-446655440511/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 200

  Scenario: Disable with invalid reason should fail
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440521 | Charlie   | Brown    | charlie521 | charlie521@test.com | pass123  |
    And the following teams exist:
      | id                                   | name        | creator_id                           |
      | 750e8400-e29b-41d4-a716-446655440521 | Normal Team | 850e8400-e29b-41d4-a716-446655440521 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/team/750e8400-e29b-41d4-a716-446655440521/disable" with body:
      """
      {
        "reason": "invalid_reason"
      }
      """
    Then the response status code should be 422

  Scenario: Non-admin cannot disable teams
    Given the following users exist:
      | id                                   | firstname | lastname | username | email             | password |
      | 850e8400-e29b-41d4-a716-446655440531 | David     | Miller   | david531 | david531@test.com | pass123  |
    And the following teams exist:
      | id                                   | name       | creator_id                           |
      | 750e8400-e29b-41d4-a716-446655440531 | David Team | 850e8400-e29b-41d4-a716-446655440531 |
    And I am authenticated as "david531@test.com" with password "pass123"
    When I send a POST request to "/backoffice/team/750e8400-e29b-41d4-a716-446655440531/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 401

  Scenario: Disable non-existent team returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/team/999e8400-e29b-41d4-a716-446655440999/disable" with body:
      """
      {
        "reason": "spam"
      }
      """
    Then the response status code should be 404
