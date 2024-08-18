<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\Update;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\Exception\PasswordMismatchException;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\ValueObject\EmailValue;
use App\Contexts\Web\User\Domain\ValueObject\FirstnameValue;
use App\Contexts\Web\User\Domain\ValueObject\LastnameValue;
use App\Contexts\Web\User\Domain\ValueObject\UsernameValue;

final readonly class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private UserUpdater $updater,
    )
    {
    }

    /**
     * @throws PasswordMismatchException
     */
    public function __invoke(UpdateUserCommand $command): void
    {
        $id = new Uuid($command->id);
        $firstname = new FirstnameValue($command->firstname);
        $lastname = new LastnameValue($command->lastname);
        $email = new EmailValue($command->email);
        $username = new UsernameValue($command->username);

        $this->updater->__invoke($id, $firstname, $lastname, $username, $email);
    }
}