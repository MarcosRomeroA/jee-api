<?php declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\CQRS\Command\Command;

final readonly class UpdateUserProfilePhotoCommand implements Command
{
    public function __construct(
        public string $id,
        public string $imageTempPath,
    )
    {
    }
}