@backoffice @admin
Feature: Delete Admin (Soft Delete)
  In order to manage admins
  As an admin
  I want to soft delete admin accounts

  Scenario: Successfully delete an admin
    Given the following admins exist:
      | id                                   | name       | user        | password |
      | 750e8400-e29b-41d4-a716-446655440072 | Test Admin | testadmin72 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a DELETE request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440072"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Delete non-existent admin should fail
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a DELETE request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440099"
    Then the response status code should be 404
    And the JSON response should have "code" with value "admin_not_found_exception"

  Scenario: Delete without authentication should fail
    Given the following admins exist:
      | id                                   | name       | user        | password |
      | 750e8400-e29b-41d4-a716-446655440091 | Test Admin | testadmin91 | admin    |
    When I send a DELETE request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440091"
    Then the response status code should be 401

  Scenario: Deleted admin should not appear in default search
    Given the following admins exist:
      | id                                   | name       | user         | password |
      | 750e8400-e29b-41d4-a716-446655440102 | Test Admin | testadmin102 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a DELETE request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440102"
    Then the response status code should be 200
    When I send a GET request to "/api/backoffice/admins"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"

  Scenario: Deleted admin should appear when including deleted
    Given the following admins exist:
      | id                                   | name       | user         | password |
      | 750e8400-e29b-41d4-a716-446655440112 | Test Admin | testadmin112 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a DELETE request to "/api/backoffice/admin/750e8400-e29b-41d4-a716-446655440112"
    Then the response status code should be 200
    When I send a GET request to "/api/backoffice/admins?includeDeleted=true"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "2"
