@team @auth
Feature: Leave Team
  In order to manage my team memberships
  As an authenticated user
  I want to be able to leave a team

  Scenario: Successfully leave a team as a member
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a team
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Team To Leave"
      }
      """
    Then the response status code should be 200
    # Request access with another user's team (simulated by accepting a request)
    # For this test, we need to add a member first via the accept request flow
    # Create a second team with tester2 and have tester1 join it
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Team With Members"
      }
      """
    Then the response status code should be 200
    # tester1 requests access to tester2's team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester2 accepts the request
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=950e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Now tester1 can leave the team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a POST request to "/api/team/950e8400-e29b-41d4-a716-446655440002/leave" with body:
      """
      {}
      """
    Then the response status code should be 200

  Scenario: Owner cannot leave their own team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a team
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Owner Team"
      }
      """
    Then the response status code should be 200
    # Try to leave as owner
    When I send a POST request to "/api/team/950e8400-e29b-41d4-a716-446655440003/leave" with body:
      """
      {}
      """
    Then the response status code should be 409

  Scenario: Cannot leave a team you are not a member of
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create a team with tester2
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/950e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "name": "Not My Team"
      }
      """
    Then the response status code should be 200
    # tester1 tries to leave a team they are not a member of
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a POST request to "/api/team/950e8400-e29b-41d4-a716-446655440004/leave" with body:
      """
      {}
      """
    Then the response status code should be 404

  Scenario: Cannot leave a non-existent team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a POST request to "/api/team/999e9999-e99b-99d9-a999-999999999999/leave" with body:
      """
      {}
      """
    Then the response status code should be 404
