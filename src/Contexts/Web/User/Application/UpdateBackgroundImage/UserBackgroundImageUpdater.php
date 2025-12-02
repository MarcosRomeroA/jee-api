<?php

declare(strict_types=1);

namespace App\Contexts\Web\User\Application\UpdateBackgroundImage;

use App\Contexts\Shared\Domain\FileManager\ImageUploader;
use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Web\User\Domain\UserRepository;
use App\Contexts\Web\User\Domain\ValueObject\BackgroundImageValue;

final readonly class UserBackgroundImageUpdater
{
    public function __construct(
        private UserRepository $repository,
        private ImageUploader $imageUploader,
    ) {
    }

    public function __invoke(Uuid $userId, string $base64Image): void
    {
        $user = $this->repository->findById($userId);

        $filename = $this->imageUploader->upload($base64Image, 'user/' . $userId->value() . '/background');

        $user->setBackgroundImage(new BackgroundImageValue($filename));

        $this->repository->save($user);
    }
}
