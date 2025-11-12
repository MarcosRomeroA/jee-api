<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\Player\Domain\ValueObject;

use App\Contexts\Web\Player\Domain\ValueObject\UsernameValue;
use PHPUnit\Framework\TestCase;

final class UsernameValueTest extends TestCase
{
    /** @test */
    public function it_should_create_valid_username(): void
    {
        $username = new UsernameValue('validuser123');

        $this->assertEquals('validuser123', $username->value());
    }

    /** @test */
    public function it_should_fail_with_empty_username(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');

        new UsernameValue('');
    }

    /** @test */
    public function it_should_fail_with_long_username(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot exceed 100 characters');

        new UsernameValue(str_repeat('a', 101));
    }

    /** @test */
    public function it_should_accept_username_with_100_characters(): void
    {
        $username = new UsernameValue(str_repeat('a', 100));

        $this->assertEquals(100, strlen($username->value()));
    }

    /** @test */
    public function it_should_be_equal_to_another_with_same_value(): void
    {
        $username1 = new UsernameValue('testuser');
        $username2 = new UsernameValue('testuser');

        $this->assertTrue($username1->equals($username2));
    }

    /** @test */
    public function it_should_not_be_equal_to_another_with_different_value(): void
    {
        $username1 = new UsernameValue('testuser');
        $username2 = new UsernameValue('anotheruser');

        $this->assertFalse($username1->equals($username2));
    }

    /** @test */
    public function it_should_convert_to_string(): void
    {
        $username = new UsernameValue('testuser');

        $this->assertEquals('testuser', (string) $username);
    }
}

