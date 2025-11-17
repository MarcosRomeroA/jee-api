@user
Feature: Confirm Email
  In order to activate my account
  As a registered user
  I want to confirm my email address

  Scenario: Successfully confirm email with valid token
    Given the following users exist:
      | id                                   | firstname | lastname | username       | email                  | password    |
      | 750e8400-e29b-41d4-a716-446655440800 | Pending   | User     | pendinguser800 | pending800@example.com | password123 |
    And the following email confirmations exist:
      | user_id                              | token                                | confirmed_at |
      | 750e8400-e29b-41d4-a716-446655440800 | 123e4567-e89b-42d3-a456-426614174000 | null         |
    When I send a GET request to "/api/verify/123e4567-e89b-42d3-a456-426614174000"
    Then the response status code should be 200

  Scenario: Confirm email with already confirmed token
    Given the following users exist:
      | id                                   | firstname | lastname | username       | email                  | password    |
      | 750e8400-e29b-41d4-a716-446655440801 | Confirmed | User     | confirmeduser1 | confirmed1@example.com | password123 |
    And the following email confirmations exist:
      | user_id                              | token                                | confirmed_at        |
      | 750e8400-e29b-41d4-a716-446655440801 | 223e4567-e89b-42d3-a456-426614174001 | 2024-01-01 12:00:00 |
    When I send a GET request to "/api/verify/223e4567-e89b-42d3-a456-426614174001"
    Then the response status code should be 400

  Scenario: Confirm email with invalid token
    When I send a GET request to "/api/verify/invalid-token-format"
    Then the response status code should be 404

  Scenario: Confirm email with expired token
    Given the following users exist:
      | id                                   | firstname | lastname | username       | email                  | password    |
      | 750e8400-e29b-41d4-a716-446655440802 | Expired   | User     | expireduser802 | expired802@example.com | password123 |
    And the following email confirmations exist:
      | user_id                              | token                                | confirmed_at | expires_at          |
      | 750e8400-e29b-41d4-a716-446655440802 | 323e4567-e89b-42d3-a456-426614174002 | null         | 2020-01-01 12:00:00 |
    When I send a GET request to "/api/verify/323e4567-e89b-42d3-a456-426614174002"
    Then the response status code should be 400
