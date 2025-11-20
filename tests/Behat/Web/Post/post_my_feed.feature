@post
Feature: My Feed
  In order to view posts from users I follow
  As an authenticated user
  I want to retrieve my personalized feed

  Scenario: Successfully retrieve my feed
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/my-feed"
    Then the response status code should be 200

  Scenario: Retrieve my feed with pagination
    Given I am authenticated as "tester1@test.com" with password "12345678"
    When I send a GET request to "/api/my-feed?page=1&limit=10"
    Then the response status code should be 200
