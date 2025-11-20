@user
Feature: Bidirectional Follow Relationship
  In order to ensure follow relationships work correctly in both directions
  As an authenticated user
  I want to verify that when User A follows User B, both users see the correct relationship

  Background:
    # Using static test users (tester1, tester2) - no need to create them

  Scenario: User A follows User B - Verify bidirectional relationship
    # User A follows User B
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Verify User A sees User B in their "followings" list
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followings"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester2"
    # Switch to User B and verify they see User A in their "followers" list
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440002/followers"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester1"
    # Verify User B's followings list is empty (they haven't followed anyone)
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440002/followings"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "0"

  Scenario: User A follows User B, then unfollows - Verify bidirectional update
    # User A follows User B
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Verify the relationship exists
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followings"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"
    # User A unfollows User B
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/unfollow" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Verify User A's followings list is now empty
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followings"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "0"
    # Verify User B's followers list is also empty
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440002/followers"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "0"

  Scenario: Mutual following - User A and User B follow each other
    # User A follows User B
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440002/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    # User B follows User A
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440001/follow" with body:
      """
      {}
      """
    Then the response status code should be 200
    # Verify User A has 1 follower (User B) and 1 following (User B)
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followers"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester2"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440001/followings"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester2"
    # Verify User B has 1 follower (User A) and 1 following (User A)
    Given I am authenticated as "tester2@test.com" with password "12345678"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440002/followers"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester1"
    When I send a GET request to "/api/user/550e8400-e29b-41d4-a716-446655440002/followings"
    Then the response status code should be 200
    And the response metadata should have "total" property with value "1"
    And the response data should contain user with username "tester1"
