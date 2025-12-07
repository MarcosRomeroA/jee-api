@user
Feature: User Preferences
  In order to customize my experience
  As an authenticated user
  I want to manage my preferences

  Scenario: Successfully update user preferences
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark",
        "lang": "en"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update preferences to light theme and spanish
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "light",
        "lang": "es"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update preferences to portuguese language
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark",
        "lang": "pt"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Update preferences without authentication
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark",
        "lang": "en"
      }
      """
    Then the response status code should be 401

  Scenario: Update preferences with invalid theme
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "invalid",
        "lang": "en"
      }
      """
    Then the response status code should be 422

  Scenario: Update preferences with invalid language
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark",
        "lang": "fr"
      }
      """
    Then the response status code should be 422

  Scenario: Update preferences with missing theme
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "lang": "en"
      }
      """
    Then the response status code should be 422

  Scenario: Update preferences with missing language
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark"
      }
      """
    Then the response status code should be 422

  Scenario: Login returns preferences in response
    Given I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 200
    And the response should have "preferences" property

  Scenario: Login returns updated preferences after change
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a PUT request to "/api/user/preference" with body:
      """
      {
        "theme": "dark",
        "lang": "en"
      }
      """
    Then the response status code should be 200
    When I send a POST request to "/api/login" with body:
      """
      {
        "email": "tester1@test.com",
        "password": "12345678"
      }
      """
    Then the response status code should be 200
    And the response should have "preferences" property
    And the JSON node "data.preferences.theme" should be equal to "dark"
    And the JSON node "data.preferences.lang" should be equal to "en"
