@team @auth
Feature: Remove User from Team
  In order to manage my team members
  As a team creator or leader
  I want to be able to remove members from the team

  Scenario: Creator can remove a member from the team
    # tester1 creates a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440001" with body:
      """
      {
        "name": "Team Remove Test 1"
      }
      """
    Then the response status code should be 200
    # tester2 requests access
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440001/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts the request
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 (creator) removes tester2
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440001/user/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200

  Scenario: Leader can remove a member from the team
    # tester1 creates a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440002" with body:
      """
      {
        "name": "Team Remove Test 2"
      }
      """
    Then the response status code should be 200
    # tester2 requests access
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts the request
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester3 requests access
    Given I am authenticated as "tester3@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440002/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts tester3
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId2"
    When I send a PUT request to "/api/team/request/{requestId2}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 makes tester2 the leader
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440002/leader/550e8400-e29b-41d4-a716-446655440002" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester2 (leader) removes tester3
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440002/user/550e8400-e29b-41d4-a716-446655440003"
    Then the response status code should be 200

  Scenario: Leader cannot remove the creator
    # tester1 creates a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440003" with body:
      """
      {
        "name": "Team Remove Test 3"
      }
      """
    Then the response status code should be 200
    # tester2 requests access
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440003/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts the request
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440003"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 makes tester2 the leader
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440003/leader/550e8400-e29b-41d4-a716-446655440002" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester2 (leader) tries to remove tester1 (creator)
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440003/user/550e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 409
    And the JSON response should have "code" with value "cannot_remove_creator"

  Scenario: Creator cannot remove themselves
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440004" with body:
      """
      {
        "name": "Team Remove Test 4"
      }
      """
    Then the response status code should be 200
    # tester1 tries to remove themselves
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440004/user/550e8400-e29b-41d4-a716-446655440001"
    Then the response status code should be 409
    And the JSON response should have "code" with value "cannot_remove_self"

  Scenario: Regular member cannot remove other members
    # tester1 creates a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440005" with body:
      """
      {
        "name": "Team Remove Test 5"
      }
      """
    Then the response status code should be 200
    # tester2 requests access
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440005/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts tester2
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440005"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId"
    When I send a PUT request to "/api/team/request/{requestId}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester3 requests access
    Given I am authenticated as "tester3@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440005/request-access" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester1 accepts tester3
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/requests?teamId=960e8400-e29b-41d4-a716-446655440005"
    Then the response status code should be 200
    And I save the value of JSON node "requests[0].id" as "requestId2"
    When I send a PUT request to "/api/team/request/{requestId2}/accept" with body:
      """
      {}
      """
    Then the response status code should be 200
    # tester2 (regular member) tries to remove tester3
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440005/user/550e8400-e29b-41d4-a716-446655440003"
    Then the response status code should be 403

  Scenario: Cannot remove user from non-existent team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a DELETE request to "/api/team/999e9999-e99b-99d9-a999-999999999999/user/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 404

  Scenario: Cannot remove user who is not a member
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440006" with body:
      """
      {
        "name": "Team Remove Test 6"
      }
      """
    Then the response status code should be 200
    # tester1 tries to remove tester2 who is not a member
    When I send a DELETE request to "/api/team/960e8400-e29b-41d4-a716-446655440006/user/550e8400-e29b-41d4-a716-446655440002"
    Then the response status code should be 404
