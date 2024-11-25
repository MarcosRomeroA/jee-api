<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\CQRS\Command\CommandHandler;
use App\Contexts\Shared\Domain\ValueObject\Uuid;

final readonly class UpdateUserProfilePhotoCommandHandler implements CommandHandler
{
    public function __construct(
        private UserProfilePhotoUpdater $updater,
    )
    {
    }

    public function __invoke(UpdateUserProfilePhotoCommand $command): void
    {
        $id = new Uuid($command->id);

        $this->updater->__invoke($id, $command->imageTempPath, $command->filename);
    }
}