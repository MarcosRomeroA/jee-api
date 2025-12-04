@event @backoffice
Feature: Delete Event (Backoffice)
  In order to manage events
  As an admin
  I want to delete events

  Scenario: Successfully delete an event
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440110" with body:
      """
      {
        "name": "Event To Delete",
        "type": "virtual",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T12:00:00+00:00"
      }
      """
    Then the response status code should be 200
    When I send a DELETE request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440110"
    Then the response status code should be 200
    And the response should be empty

  Scenario: Fail to delete non-existent event
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a DELETE request to "/backoffice/event/550e8400-e29b-41d4-a716-446655449999"
    Then the response status code should be 404

  Scenario: Fail to delete event without admin authentication
    Given I am not authenticated
    When I send a DELETE request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440110"
    Then the response status code should be 401
