@event
Feature: Search Events
  In order to discover events
  As a user
  I want to search for upcoming events

  Background:
    Given I am authenticated as admin with user "admin" and password "admin"
    # Create test events for search
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440130" with body:
      """
      {
        "name": "Virtual Gaming Event",
        "description": "Online gaming event",
        "type": "virtual",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T18:00:00+00:00"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440131" with body:
      """
      {
        "name": "Presencial Tournament",
        "description": "In-person tournament",
        "type": "presencial",
        "startAt": "2025-12-21T09:00:00+00:00",
        "endAt": "2025-12-21T20:00:00+00:00"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440132" with body:
      """
      {
        "name": "Valorant Championship",
        "description": "Valorant competition",
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "type": "virtual",
        "startAt": "2025-12-22T14:00:00+00:00",
        "endAt": "2025-12-22T22:00:00+00:00"
      }
      """
    Then the response status code should be 200

  Scenario: Search all upcoming events
    Given I am not authenticated
    When I send a GET request to "/api/events"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search events with pagination
    Given I am not authenticated
    When I send a GET request to "/api/events?limit=2&offset=0"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "limit" property with value "2"
    And the response metadata should have "offset" property with value "0"

  Scenario: Search events filtered by type
    Given I am not authenticated
    When I send a GET request to "/api/events?type=virtual"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search events filtered by game
    Given I am not authenticated
    When I send a GET request to "/api/events?gameId=550e8400-e29b-41d4-a716-446655440080"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search events without authentication (public endpoint)
    Given I am not authenticated
    When I send a GET request to "/api/events"
    Then the response status code should be 200
    And the response should contain pagination structure
