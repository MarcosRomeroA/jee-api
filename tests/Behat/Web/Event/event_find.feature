@event
Feature: Find Event
  In order to view event details
  As a user
  I want to find a specific event

  Scenario: Successfully find an event
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440120" with body:
      """
      {
        "name": "Findable Event",
        "description": "This event can be found",
        "type": "presencial",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-22T18:00:00+00:00"
      }
      """
    Then the response status code should be 200
    Given I am not authenticated
    When I send a GET request to "/api/event/550e8400-e29b-41d4-a716-446655440120"
    Then the response status code should be 200
    And the response should have property "id" with value "550e8400-e29b-41d4-a716-446655440120"
    And the response should have property "name" with value "Findable Event"
    And the response should have property "description" with value "This event can be found"
    And the response should have property "type" with value "presencial"

  Scenario: Find event with game association
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440121" with body:
      """
      {
        "name": "Valorant Event",
        "description": "Event with game",
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "type": "virtual",
        "startAt": "2025-12-25T14:00:00+00:00",
        "endAt": "2025-12-25T20:00:00+00:00"
      }
      """
    Then the response status code should be 200
    Given I am not authenticated
    When I send a GET request to "/api/event/550e8400-e29b-41d4-a716-446655440121"
    Then the response status code should be 200
    And the response should have property "name" with value "Valorant Event"
    And the response should have property "game" with value "Valorant"

  Scenario: Find event without authentication (public endpoint)
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440122" with body:
      """
      {
        "name": "Public Event",
        "type": "virtual",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T12:00:00+00:00"
      }
      """
    Then the response status code should be 200
    Given I am not authenticated
    When I send a GET request to "/api/event/550e8400-e29b-41d4-a716-446655440122"
    Then the response status code should be 200
    And the response should have property "name" with value "Public Event"

  Scenario: Fail to find non-existent event
    Given I am not authenticated
    When I send a GET request to "/api/event/550e8400-e29b-41d4-a716-446655449999"
    Then the response status code should be 404
