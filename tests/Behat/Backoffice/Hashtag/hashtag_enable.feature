@backoffice @hashtag
Feature: Enable hashtag from backoffice
  As an admin
  I want to enable previously disabled hashtags
  So that hashtags can be restored after review

  Scenario: Successfully enable a disabled hashtag
    Given the following hashtags exist:
      | id                                   | tag        | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440201 | wasblocked |    50 | true     |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440201/enable"
    Then the response status code should be 200

  Scenario: Enabled hashtag should appear as enabled in search
    Given the following hashtags exist:
      | id                                   | tag        | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440211 | testenable |    50 | true     |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440211/enable"
    Then the response status code should be 200
    When I send a GET request to "/backoffice/hashtags?tag=testenable"
    Then the response status code should be 200
    And the response should contain "disabled" with value "false"

  Scenario: Non-admin cannot enable hashtags
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440221 | Regular   | User     | regular221 | regular221@test.com | pass123  |
    And the following hashtags exist:
      | id                                   | tag     | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440221 | blocked |   100 | true     |
    And I am authenticated as "regular221@test.com" with password "pass123"
    When I send a PUT request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440221/enable"
    Then the response status code should be 401

  Scenario: Enable non-existent hashtag returns 404
    Given I am authenticated as admin with user "admin" and password "admin"
    When I send a PUT request to "/backoffice/hashtag/999e8400-e29b-41d4-a716-446655440999/enable"
    Then the response status code should be 404

  Scenario: Enable without authentication should fail
    Given the following hashtags exist:
      | id                                   | tag     | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440231 | blocked |   100 | true     |
    When I send a PUT request to "/backoffice/hashtag/660e8400-e29b-41d4-a716-446655440231/enable"
    Then the response status code should be 401
