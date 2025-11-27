@backoffice @admin
Feature: Search Admins
  In order to manage admins
  As an admin
  I want to search and filter admins

  Scenario: Successfully search all admins
    Given the following admins exist:
      | id                                   | name         | user          | password |
      | 750e8400-e29b-41d4-a716-446655440032 | John Admin   | johnadmin32   | admin    |
      | 750e8400-e29b-41d4-a716-446655440033 | Jane Manager | janemanager33 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/admins"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "total" property with value "3"

  Scenario: Search admins by name
    Given the following admins exist:
      | id                                   | name         | user          | password |
      | 750e8400-e29b-41d4-a716-446655440042 | John Admin   | johnadmin42   | admin    |
      | 750e8400-e29b-41d4-a716-446655440043 | Jane Manager | janemanager43 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/admins?name=John"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search admins by user
    Given the following admins exist:
      | id                                   | name         | user          | password |
      | 750e8400-e29b-41d4-a716-446655440052 | John Admin   | johnadmin52   | admin    |
      | 750e8400-e29b-41d4-a716-446655440053 | Jane Manager | janemanager53 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/admins?user=admin"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search admins with pagination
    Given the following admins exist:
      | id                                   | name         | user          | password |
      | 750e8400-e29b-41d4-a716-446655440062 | John Admin   | johnadmin62   | admin    |
      | 750e8400-e29b-41d4-a716-446655440063 | Jane Manager | janemanager63 | admin    |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/admins?limit=2&offset=0"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "limit" property with value "2"
    And the response metadata should have "offset" property with value "0"

  Scenario: Search without authentication should fail
    When I send a GET request to "/backoffice/admins"
    Then the response status code should be 401
