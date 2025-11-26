@backoffice @admin
Feature: Create Admin (UPSERT)
  In order to manage the backoffice
  As an admin
  I want to create or update admin accounts

  Scenario: Successfully create an admin
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "John Admin",
        "user": "johnadmin",
        "password": "SecureAdmin123!"
      }
      """
    Then the response status code should be 201

  Scenario: Successfully update an existing admin (UPSERT)
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Test Admin",
        "user": "testadmin002",
        "password": "InitialPassword123!"
      }
      """
    Then the response status code should be 201
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Updated Test Admin",
        "user": "testadmin002",
        "password": "UpdatedPassword123!"
      }
      """
    Then the response status code should be 201

  Scenario: Create admin with existing user but different ID should fail
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Another Admin",
        "user": "admin",
        "password": "SecureAdmin123!"
      }
      """
    Then the response status code should be 409
    And the JSON response should have "code" with value "admin_user_already_exists_exception"

  Scenario: Create admin without authentication should fail
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "name": "Unauthorized Admin",
        "user": "unauthorized",
        "password": "SecureAdmin123!"
      }
      """
    Then the response status code should be 401

  Scenario: Create admin with invalid user format
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440005" with body:
      """
      {
        "name": "Invalid Admin",
        "user": "a",
        "password": "SecureAdmin123!"
      }
      """
    Then the response status code should be 422

  Scenario: Create admin with weak password
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440006" with body:
      """
      {
        "name": "Weak Password Admin",
        "user": "weakadmin",
        "password": "123"
      }
      """
    Then the response status code should be 422
