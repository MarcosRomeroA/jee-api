<?php declare(strict_types=1);

namespace App\Tests\Unit\Web\User\Application;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Application\Create\UserCreator;
use App\Contexts\Web\User\Domain\Exception\UsernameAlreadyExistsException;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use App\Tests\Unit\Web\User\Domain\UserMother;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class UserCreatorTest extends TestCase
{
    private UserRepository|MockObject $repository;
    private EventBus|MockObject $eventBus;
    private UserCreator $creator;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->creator = new UserCreator($this->repository, $this->eventBus);
    }

    public function testItShouldCreateAUser(): void
    {
        $id = Uuid::random();
        $firstname = new FirstnameValue("John");
        $lastname = new LastnameValue("Doe");
        $username = new UsernameValue("johndoe");
        $email = new EmailValue("john@example.com");
        $password = new PasswordValue(
            password_hash("password123", PASSWORD_BCRYPT),
        );

        // Simular que el usuario no existe (lanza excepción en findById)
        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new \Exception("User not found"));

        // Simular que no existe usuario con ese username (no lanza excepción)
        $this->repository
            ->expects($this->once())
            ->method("checkIfUsernameExists")
            ->with($username);

        $this->repository
            ->expects($this->once())
            ->method("save")
            ->with(
                $this->callback(function ($user) use ($id) {
                    return $user->getId()->value() === $id->value();
                }),
            );

        $this->eventBus->expects($this->once())->method("publish");

        $this->creator->__invoke(
            $id,
            $firstname,
            $lastname,
            $username,
            $email,
            $password,
        );
    }

    public function testItShouldThrowExceptionWhenUsernameAlreadyExists(): void
    {
        $id = Uuid::random();
        $firstname = new FirstnameValue("John");
        $lastname = new LastnameValue("Doe");
        $username = new UsernameValue("johndoe");
        $email = new EmailValue("john@example.com");
        $password = new PasswordValue(
            password_hash("password123", PASSWORD_BCRYPT),
        );

        // Simular que el usuario no existe por ID
        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willThrowException(new \Exception("User not found"));

        // Simular que ya existe un usuario con ese username
        $this->repository
            ->expects($this->once())
            ->method("checkIfUsernameExists")
            ->with($username)
            ->willThrowException(new UsernameAlreadyExistsException());

        $this->expectException(UsernameAlreadyExistsException::class);

        $this->creator->__invoke(
            $id,
            $firstname,
            $lastname,
            $username,
            $email,
            $password,
        );
    }

    public function testItShouldUpdateExistingUser(): void
    {
        $id = Uuid::random();
        $firstname = new FirstnameValue("John");
        $lastname = new LastnameValue("Doe");
        $username = new UsernameValue("johndoe");
        $email = new EmailValue("john@example.com");
        $password = new PasswordValue(
            password_hash("password123", PASSWORD_BCRYPT),
        );

        // Simular que el usuario existe
        $existingUser = UserMother::create(id: $id);
        $this->repository
            ->expects($this->once())
            ->method("findById")
            ->with($id)
            ->willReturn($existingUser);

        $this->repository->expects($this->once())->method("save");

        $this->eventBus->expects($this->once())->method("publish");

        // Llamar sin password para actualizar
        $this->creator->__invoke(
            $id,
            $firstname,
            $lastname,
            $username,
            $email,
            null,
        );
    }
}
