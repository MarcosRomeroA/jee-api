<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Domain;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\EmailConfirmation;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;
use PHPUnit\Framework\TestCase;

final class EmailConfirmationTest extends TestCase
{
    public function testItShouldCreateAnEmailConfirmation(): void
    {
        $id = Uuid::random();
        $user = UserMother::random();
        $token = EmailConfirmationToken::generate();
        $expiresAt = (new \DateTimeImmutable())->modify('+24 hours');

        $emailConfirmation = new EmailConfirmation($id, $user, $token, $expiresAt);

        $this->assertEquals($id, $emailConfirmation->id());
        $this->assertEquals($user, $emailConfirmation->user());
        $this->assertEquals($token, $emailConfirmation->token());
        $this->assertFalse($emailConfirmation->isConfirmed());
        $this->assertFalse($emailConfirmation->isExpired());
    }

    public function testItShouldConfirmEmail(): void
    {
        $emailConfirmation = EmailConfirmationMother::random();

        $emailConfirmation->confirm();

        $this->assertTrue($emailConfirmation->isConfirmed());
        $this->assertNotNull($emailConfirmation->confirmedAt());
    }

    public function testItShouldDetectExpiredToken(): void
    {
        $emailConfirmation = EmailConfirmationMother::expired();

        $this->assertTrue($emailConfirmation->isExpired());
    }

    public function testItShouldThrowExceptionWhenConfirmingAlreadyConfirmed(): void
    {
        $emailConfirmation = EmailConfirmationMother::confirmed();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email already confirmed');

        $emailConfirmation->confirm();
    }

    public function testItShouldThrowExceptionWhenConfirmingExpiredToken(): void
    {
        $emailConfirmation = EmailConfirmationMother::expired();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Confirmation token has expired');

        $emailConfirmation->confirm();
    }
}

