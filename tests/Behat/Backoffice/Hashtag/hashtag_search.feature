@backoffice @hashtag
Feature: Search Hashtags from Backoffice
  In order to moderate hashtags
  As an admin
  I want to search and filter hashtags

  Scenario: Successfully search all hashtags
    Given the following hashtags exist:
      | id                                   | tag      | count |
      | 660e8400-e29b-41d4-a716-446655440001 | gaming   |   100 |
      | 660e8400-e29b-41d4-a716-446655440002 | esports  |    50 |
      | 660e8400-e29b-41d4-a716-446655440003 | valorant |    30 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/hashtags"
    Then the response status code should be 200
    And the response should contain pagination structure

  Scenario: Search hashtags by query
    Given the following hashtags exist:
      | id                                   | tag      | count |
      | 660e8400-e29b-41d4-a716-446655440011 | gaming   |   100 |
      | 660e8400-e29b-41d4-a716-446655440012 | gamer    |    50 |
      | 660e8400-e29b-41d4-a716-446655440013 | valorant |    30 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/hashtags?q=gam"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "2"

  Scenario: Search hashtags by exact tag
    Given the following hashtags exist:
      | id                                   | tag     | count |
      | 660e8400-e29b-41d4-a716-446655440021 | gaming  |   100 |
      | 660e8400-e29b-41d4-a716-446655440022 | esports |    50 |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/hashtags?tag=gaming"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "1"

  Scenario: Search only disabled hashtags
    Given the following hashtags exist:
      | id                                   | tag       | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440031 | gaming    |   100 | false    |
      | 660e8400-e29b-41d4-a716-446655440032 | badword   |    50 | true     |
      | 660e8400-e29b-41d4-a716-446655440033 | offensive |    30 | true     |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/hashtags?disabled=true"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "2"

  Scenario: Search only enabled hashtags
    Given the following hashtags exist:
      | id                                   | tag       | count | disabled |
      | 660e8400-e29b-41d4-a716-446655440041 | gaming    |   100 | false    |
      | 660e8400-e29b-41d4-a716-446655440042 | esports   |    50 | false    |
      | 660e8400-e29b-41d4-a716-446655440043 | offensive |    30 | true     |
    And I am authenticated as admin with user "admin" and password "admin"
    When I send a GET request to "/backoffice/hashtags?disabled=false"
    Then the response status code should be 200
    And the response should contain pagination structure
    And the response metadata should have "count" property with value "2"

  Scenario: Search without authentication should fail
    When I send a GET request to "/backoffice/hashtags"
    Then the response status code should be 401

  Scenario: Non-admin cannot search hashtags
    Given the following users exist:
      | id                                   | firstname | lastname | username   | email               | password |
      | 850e8400-e29b-41d4-a716-446655440051 | Regular   | User     | regular051 | regular051@test.com | pass123  |
    And I am authenticated as "regular051@test.com" with password "pass123"
    When I send a GET request to "/backoffice/hashtags"
    Then the response status code should be 401
