@team @auth
Feature: Team Requests
  In order to manage team join requests
  As an authenticated user
  I want to view and manage pending team requests

  Scenario: Get all pending team requests
    # Los usuarios tester1, tester2, tester3 ya existen en la BD (migración Version20251119000001)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Alpha Team",
        "description": "First team for testing"
      }
      """
    Then the response status code should be 200
    # Create another team as john
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Beta Team",
        "description": "Second team for testing"
      }
      """
    Then the response status code should be 200
    # Tester2 requests to join Alpha Team
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440001/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Tester3 requests to join Beta Team
    Given I am authenticated as "tester3@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get all pending requests
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 2 elements
    And the response should contain a request for team "Alpha Team" by user "tester2" with status "pending"
    And the response should contain a request for team "Beta Team" by user "tester3" with status "pending"

  Scenario: Get pending requests shows only pending ones
    # Los usuarios tester1, tester2 ya existen en la BD (migración Version20251119000001)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Gamma Team",
        "description": "Third team for testing"
      }
      """
    Then the response status code should be 200
    # Tester2 requests to join
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440003/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Check there's one pending request
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    # Accept the request as tester1
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    And the JSON node "requests[0].status" should be equal to "pending"
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Now check that there are no pending requests
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements

  Scenario: Empty pending requests list when no requests exist
    # El usuario tester1 ya existe en la BD (migración Version20251119000001)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements

  Scenario: Team creator cannot request to join own team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440010" with body:
      """
      {
        "name": "My Own Team"
      }
      """
    Then the response status code should be 200
    # Creator tries to request access to own team
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440010/request-access" with body:
      """
      {}
      """
    Then the response status code should be 409

  Scenario: Existing member cannot request to join team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440011" with body:
      """
      {
        "name": "Team For Member Test"
      }
      """
    Then the response status code should be 200
    # Tester2 requests to join
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440011/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Tester1 accepts the request
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=750e8400-e29b-41d4-a716-446655440011"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Tester2 (now a member) tries to request again
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440011/request-access" with body:
      """
      {}
      """
    Then the response status code should be 409

  Scenario: Filter pending requests by teamId
    # Los usuarios tester1, tester2, tester3 ya existen en la BD (migración Version20251119000001)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create two teams
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Filter Team Alpha",
        "description": "First team for filter testing"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Filter Team Beta",
        "description": "Second team for filter testing"
      }
      """
    Then the response status code should be 200
    # Tester2 requests to join Alpha Team
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440001/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Tester3 requests to join Beta Team
    Given I am authenticated as "tester3@test.com" with password "12345678"
    When I send a PUT request to "/api/team/850e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get all pending requests (should be 2)
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 2 elements
    # Filter by Alpha Team (should be 1)
    When I send a GET request to "/api/team/requests?teamId=850e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    And the response should contain a request for team "Filter Team Alpha" by user "tester2" with status "pending"
    # Filter by Beta Team (should be 1)
    When I send a GET request to "/api/team/requests?teamId=850e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    And the response should contain a request for team "Filter Team Beta" by user "tester3" with status "pending"
    # Filter by non-existent team (should be 0)
    When I send a GET request to "/api/team/requests?teamId=850e8400-e29b-41d4-a716-446655440099"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements
