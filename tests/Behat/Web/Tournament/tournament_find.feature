@tournament @auth
Feature: Find Tournament
  In order to get tournament information
  As an authenticated user
  I want to retrieve a tournament by id

  Scenario: Successfully find a tournament by id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament/750e8400-e29b-41d4-a716-446655440000"
    Then the response status code should be 200
    And the response should have "id" property
    And the response should have "name" property
    And the response should have "description" property
    And the response should have "rules" property
    And the response should have "isUserRegistered" property

  Scenario: Find tournament with non-existent id
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/tournament/999e9999-e99b-99d9-a999-999999999999"
    Then the response status code should be 404

  Scenario: isUserRegistered is true when user's team is registered in tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440020" with body:
      """
      {
        "name": "User Registration Test Tournament",
        "description": "Testing isUserRegistered flag",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-08-15T10:00:00Z",
        "endAt": "2026-08-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Create team
    When I send a PUT request to "/api/team/960e8400-e29b-41d4-a716-446655440020" with body:
      """
      {
        "name": "User Registration Test Team"
      }
      """
    Then the response status code should be 200
    # Register team in tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440020/team/960e8400-e29b-41d4-a716-446655440020" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Find tournament - should have isUserRegistered = true
    When I send a GET request to "/api/tournament/960e8400-e29b-41d4-a716-446655440020"
    Then the response status code should be 200
    And the response should have property "isUserRegistered" with value "true"

  Scenario: isUserRegistered is false when user's team is not registered in tournament
    Given I am authenticated as "tester1@test.com" with password "12345678"
    # Create tournament
    When I send a PUT request to "/api/tournament/960e8400-e29b-41d4-a716-446655440021" with body:
      """
      {
        "name": "Not Registered Test Tournament",
        "description": "Testing isUserRegistered false",
        "gameId": "750e8400-e29b-41d4-a716-446655440001",
        "statusId": "a50e8400-e29b-41d4-a716-446655440001",
        "maxTeams": 16,
        "startAt": "2026-09-15T10:00:00Z",
        "endAt": "2026-09-20T18:00:00Z"
      }
      """
    Then the response status code should be 200
    # Find tournament without registering - should have isUserRegistered = false
    When I send a GET request to "/api/tournament/960e8400-e29b-41d4-a716-446655440021"
    Then the response status code should be 200
    And the response should have property "isUserRegistered" with value "false"
