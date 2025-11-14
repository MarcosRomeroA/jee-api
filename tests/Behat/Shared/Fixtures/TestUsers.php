<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Fixtures;

/**
 * Shared test user constants to avoid duplicate entries across test contexts.
 * All test contexts should use these constants instead of creating their own users with the same credentials.
 */
final class TestUsers
{
    // Primary test user - used for authentication in most tests
    public const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';
    public const USER1_USERNAME = 'testuser';
    public const USER1_EMAIL = 'test@example.com';
    public const USER1_PASSWORD = 'password123'; // Plain text - will be hashed by PasswordValue
    public const USER1_FIRSTNAME = 'John';
    public const USER1_LASTNAME = 'Doe';

    // Secondary test user - used for interactions (follow, unfollow, conversations, etc.)
    public const USER2_ID = '550e8400-e29b-41d4-a716-446655440002';
    public const USER2_USERNAME = 'janesmith';
    public const USER2_EMAIL = 'jane@example.com';
    public const USER2_PASSWORD = 'password456';
    public const USER2_FIRSTNAME = 'Jane';
    public const USER2_LASTNAME = 'Smith';

    // Additional test user for multi-user scenarios
    public const USER3_ID = '550e8400-e29b-41d4-a716-446655440003';
    public const USER3_USERNAME = 'bobtest';
    public const USER3_EMAIL = 'bob@example.com';
    public const USER3_PASSWORD = 'password789';
    public const USER3_FIRSTNAME = 'Bob';
    public const USER3_LASTNAME = 'Test';

    private function __construct()
    {
        // Prevent instantiation
    }
}
