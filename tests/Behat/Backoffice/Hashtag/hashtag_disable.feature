@backoffice @hashtag
Feature: Disable hashtag from backoffice
  As an admin
  I want to disable hashtags
  So that inappropriate hashtags are not shown to users

  Scenario: Successfully disable a hashtag
    Given the following hashtags exist:
      | id                                   | tag        | count |
      | 660e8400-e29b-41d4-a716-446655440101 | badhashtag |    50 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440101/disable"
    Then the response status code should be 200

  Scenario: Disabled hashtag should appear as disabled in search
    Given the following hashtags exist:
      | id                                   | tag         | count |
      | 660e8400-e29b-41d4-a716-446655440111 | testdisable |    50 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440111/disable"
    Then the response status code should be 200
    When I send a GET request to "/backoffice/hashtags?tag=testdisable"
    Then the response status code should be 200
    And the response should contain "disabled" with value "true"

  Scenario: Non-admin cannot disable hashtags
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440121 | Regular   | User     | regular121 | regular121@test.com | pass123  |
    And the following hashtags exist:
      | id                                   | tag    | count |
      | 660e8400-e29b-41d4-a716-446655440121 | gaming |   100 |
    And I am authenticated as "regular121@test.com" with password "pass123"
    When I send a POST request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440121/disable"
    Then the response status code should be 401

  Scenario: Disable non-existent hashtag returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a POST request to "/backoffice/hashtag/999e8400-e29b-41d4-a716-446655440999/disable"
    Then the response status code should be 404

  Scenario: Disable without authentication should fail
    Given the following hashtags exist:
      | id                                   | tag    | count |
      | 660e8400-e29b-41d4-a716-446655440131 | gaming |   100 |
    When I send a POST request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440131/disable"
    Then the response status code should be 401
