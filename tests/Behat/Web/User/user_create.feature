@user
Feature: Create User
  In order to register in the system
  As a new user
  I want to create an account

  Scenario: Successfully create a user
    Given I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440700" with body:
      """
      {
        "firstname": "John",
        "lastname": "Doe",
        "username": "johndoe123",
        "email": "johndoe123@example.com",
        "password": "SecurePassword123!",
        "confirmationPassword": "SecurePassword123!"
      }
      """
    Then the response status code should be 200
    And the response should be empty

  Scenario: Create user with existing email
    Given I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440701" with body:
      """
      {
        "firstname": "Jane",
        "lastname": "Smith",
        "username": "janesmithnew",
        "email": "test@example.com",
        "password": "SecurePassword123!",
        "confirmationPassword": "SecurePassword123!"
      }
      """
    Then the response status code should be 400
    And the JSON response should have "code" with value "email_already_exists_exception"

  Scenario: Create user with missing required fields
    Given I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440702" with body:
      """
      {
        "firstname": "John",
        "lastname": "Doe"
      }
      """
    Then the response status code should be 422

  Scenario: Create user with invalid email format
    Given I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440703" with body:
      """
      {
        "firstname": "John",
        "lastname": "Doe",
        "username": "johndoe",
        "email": "invalid-email",
        "password": "SecurePassword123!",
        "confirmationPassword": "SecurePassword123!"
      }
      """
    Then the response status code should be 422

  Scenario: Create user with weak password
    Given I send a PUT request to "/api/user/550e8400-e29b-41d4-a716-446655440704" with body:
      """
      {
        "firstname": "John",
        "lastname": "Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "password": "123",
        "confirmationPassword": "123"
      }
      """
    Then the response status code should be 422
