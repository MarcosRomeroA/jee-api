<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Application;

use App\Contexts\Web\User\Application\ConfirmEmail\EmailConfirmer;
use App\Contexts\Web\User\Domain\EmailConfirmationRepository;
use App\Contexts\Web\User\Domain\Exception\EmailAlreadyConfirmedException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationExpiredException;
use App\Contexts\Web\User\Domain\Exception\EmailConfirmationNotFoundException;
use App\Contexts\Web\User\Domain\ValueObject\EmailConfirmationToken;
use App\Tests\Unit\Web\User\Domain\EmailConfirmationMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class EmailConfirmerTest extends TestCase
{
    private EmailConfirmationRepository|MockObject $repository;
    private EmailConfirmer $confirmer;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(EmailConfirmationRepository::class);
        $this->confirmer = new EmailConfirmer($this->repository);
    }

    public function testItShouldConfirmEmail(): void
    {
        $emailConfirmation = EmailConfirmationMother::random();
        $tokenValue = $emailConfirmation->token()->value();

        $this->repository
            ->expects($this->once())
            ->method('findByToken')
            ->with($this->callback(function (EmailConfirmationToken $token) use ($tokenValue) {
                return $token->value() === $tokenValue;
            }))
            ->willReturn($emailConfirmation);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($emailConfirmation);

        $this->confirmer->confirm($tokenValue);

        $this->assertTrue($emailConfirmation->isConfirmed());
    }

    public function testItShouldThrowExceptionWhenTokenNotFound(): void
    {
        $token = EmailConfirmationToken::generate();

        $this->repository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn(null);

        $this->expectException(EmailConfirmationNotFoundException::class);

        $this->confirmer->confirm($token->value());
    }

    public function testItShouldThrowExceptionWhenAlreadyConfirmed(): void
    {
        $emailConfirmation = EmailConfirmationMother::confirmed();
        $tokenValue = $emailConfirmation->token()->value();

        $this->repository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn($emailConfirmation);

        $this->expectException(EmailAlreadyConfirmedException::class);

        $this->confirmer->confirm($tokenValue);
    }

    public function testItShouldThrowExceptionWhenTokenExpired(): void
    {
        $emailConfirmation = EmailConfirmationMother::expired();
        $tokenValue = $emailConfirmation->token()->value();

        $this->repository
            ->expects($this->once())
            ->method('findByToken')
            ->willReturn($emailConfirmation);

        $this->expectException(EmailConfirmationExpiredException::class);

        $this->confirmer->confirm($tokenValue);
    }
}

