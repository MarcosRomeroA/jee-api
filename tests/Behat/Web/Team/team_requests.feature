@team @auth
Feature: Team Requests
  In order to manage team join requests
  As an authenticated user
  I want to view and manage pending team requests

  Scenario: Get all pending team requests
    # Create a team as john
    Given I am authenticated as "test@example.com" with password "password123"
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
    # Jane requests to join Alpha Team
    Given I am authenticated as "jane@example.com" with password "password456"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440001/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Bob requests to join Beta Team
    Given I am authenticated as "bob@example.com" with password "password789"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Get all pending requests
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 2 elements
    And the response should contain a request for team "Alpha Team" by player "jane" with status "pending"
    And the response should contain a request for team "Beta Team" by player "bob" with status "pending"

  Scenario: Get pending requests shows only pending ones
    # Create a team as john
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Gamma Team",
        "description": "Third team for testing"
      }
      """
    Then the response status code should be 200
    # Jane requests to join
    Given I am authenticated as "jane@example.com" with password "password456"
    When I send a PUT request to "/api/team/750e8400-e29b-41d4-a716-446655440003/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Check there's one pending request
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 1 element
    # Accept the request as john
    Given I am authenticated as "test@example.com" with password "password123"
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
    Given I am authenticated as "test@example.com" with password "password123"
    When I send a GET request to "/api/team/requests"
    Then the response status code should be 200
    And the JSON node "requests" should have 0 elements
