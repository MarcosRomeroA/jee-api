<?php declare(strict_types=1);

namespace App\Tests\Behat\Shared\Fixtures;

/**
 * Shared test user constants pointing to static users in the database.
 * These users are created by migration Version20251119000001 and should NEVER be modified or deleted.
 * They are READ-ONLY fixtures for tests.
 *
 * For tests that need to modify users, create temporary users within the test context.
 */
final class TestUsers
{
    // Primary test user - used for authentication in most tests
    // WARNING: This is a READ-ONLY user from the database. DO NOT MODIFY.
    public const USER1_ID = '550e8400-e29b-41d4-a716-446655440001';
    public const USER1_USERNAME = 'tester1';
    public const USER1_EMAIL = 'tester1@test.com';
    public const USER1_PASSWORD = '12345678'; // Plain text password
    public const USER1_FIRSTNAME = 'Tester';
    public const USER1_LASTNAME = 'One';

    // Secondary test user - used for interactions (follow, unfollow, conversations, etc.)
    // WARNING: This is a READ-ONLY user from the database. DO NOT MODIFY.
    public const USER2_ID = '550e8400-e29b-41d4-a716-446655440002';
    public const USER2_USERNAME = 'tester2';
    public const USER2_EMAIL = 'tester2@test.com';
    public const USER2_PASSWORD = '12345678'; // Plain text password
    public const USER2_FIRSTNAME = 'Tester';
    public const USER2_LASTNAME = 'Two';

    // Additional test user for multi-user scenarios
    // WARNING: This is a READ-ONLY user from the database. DO NOT MODIFY.
    public const USER3_ID = '550e8400-e29b-41d4-a716-446655440003';
    public const USER3_USERNAME = 'tester3';
    public const USER3_EMAIL = 'tester3@test.com';
    public const USER3_PASSWORD = '12345678'; // Plain text password
    public const USER3_FIRSTNAME = 'Tester';
    public const USER3_LASTNAME = 'Three';

    private function __construct()
    {
        // Prevent instantiation
    }
}
