<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Event\EventBus;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UserUpdater
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
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

        if ($user->getUsername() !== $username) {
            $this->userRepository->checkIfUsernameExists($username);
        }

        if ($user->getEmail() !== $email) {
            $this->userRepository->checkIfEmailExists($email);
        }

        $user->update($firstname, $lastname, $username, $email);
        $this->entityManager->flush();
        $this->bus->publish(...$user->pullDomainEvents());
    }
}