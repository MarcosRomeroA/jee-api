@team @auth
Feature: Find Team Members
  In order to see who is in a team
  As an authenticated user
  I want to retrieve all members of a team

  Scenario: Successfully find members of a team
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/550e8400-e29b-41d4-a716-446655440072/members"
    Then the response status code should be 200
    And the response should have "data" property as array

  Scenario: Find members of non-existent team returns empty array
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/team/999e9999-e99b-99d9-a999-999999999999/members"
    Then the response status code should be 200
    And the response should have "data" property as array
