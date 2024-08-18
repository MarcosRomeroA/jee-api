<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\PasswordValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class UserUpdater
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $bus,
    )
    {
    }

    public function __invoke(
        Uuid $id,
        FirstnameValue $firstname,
        LastnameValue $lastname,
        UsernameValue $username,
        EmailValue $email,
    ): void
    {
        $user = $this->userRepository->findById($id);
        $user->update($firstname, $lastname, $username, $email);
        $this->userRepository->save($user);
        $this->bus->publish(...$user->pullDomainEvents());
    }
}