@event @backoffice
Feature: Create Event (Backoffice)
  In order to manage events
  As an admin
  I want to create and update events

  Scenario: Successfully create an event
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440100" with body:
      """
      {
        "name": "Championship 2025",
        "description": "Annual esports championship",
        "type": "presencial",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-22T18:00:00+00:00"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create event with game association
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440101" with body:
      """
      {
        "name": "Valorant Tournament",
        "description": "Competitive Valorant event",
        "gameId": "550e8400-e29b-41d4-a716-446655440080",
        "type": "virtual",
        "startAt": "2025-12-25T14:00:00+00:00",
        "endAt": "2025-12-25T20:00:00+00:00"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create event without optional fields
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440102" with body:
      """
      {
        "name": "Minimal Event",
        "type": "virtual",
        "startAt": "2025-12-30T09:00:00+00:00",
        "endAt": "2025-12-30T12:00:00+00:00"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update an existing event
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440103" with body:
      """
      {
        "name": "Original Event",
        "type": "virtual",
        "startAt": "2025-12-15T10:00:00+00:00",
        "endAt": "2025-12-15T12:00:00+00:00"
      }
      """
    Then the response status code should be 200
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440103" with body:
      """
      {
        "name": "Updated Event Name",
        "description": "Updated description",
        "type": "presencial",
        "startAt": "2025-12-16T10:00:00+00:00",
        "endAt": "2025-12-16T14:00:00+00:00"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Fail to create event with missing name
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440104" with body:
      """
      {
        "description": "Missing name",
        "type": "virtual",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T12:00:00+00:00"
      }
      """
    Then the response status code should be 422

  Scenario: Fail to create event with invalid type
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440105" with body:
      """
      {
        "name": "Invalid Type Event",
        "type": "invalid_type",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T12:00:00+00:00"
      }
      """
    Then the response status code should be 422

  Scenario: Fail to create event with end date before start date
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440106" with body:
      """
      {
        "name": "Invalid Date Event",
        "type": "virtual",
        "startAt": "2025-12-22T10:00:00+00:00",
        "endAt": "2025-12-20T10:00:00+00:00"
      }
      """
    Then the response status code should be 400

  Scenario: Create event with same start and end date
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440107" with body:
      """
      {
        "name": "Single Day Event",
        "type": "presencial",
        "startAt": "2025-12-25T10:00:00+00:00",
        "endAt": "2025-12-25T10:00:00+00:00"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Fail to create event without admin authentication
    Given I am not authenticated
    When I send a PUT request to "/backoffice/event/550e8400-e29b-41d4-a716-446655440108" with body:
      """
      {
        "name": "Unauthorized Event",
        "type": "virtual",
        "startAt": "2025-12-20T10:00:00+00:00",
        "endAt": "2025-12-20T12:00:00+00:00"
      }
      """
    Then the response status code should be 401
