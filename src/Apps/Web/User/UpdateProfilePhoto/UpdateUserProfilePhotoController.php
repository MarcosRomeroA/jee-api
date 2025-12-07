<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\UpdateProfilePhoto\UpdateUserProfilePhotoCommand;
use App\Contexts\Web\User\Domain\Exception\InvalidProfileImageException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserProfilePhotoController extends ApiController
{

    public function __invoke(Request $request, string $sessionId): Response
    {
        /** @var UploadedFile|null $profilePhoto */
        $profilePhoto = $request->files->get('image');

        if ($profilePhoto === null) {
            throw new InvalidProfileImageException('No image file provided');
        }

        if (!$profilePhoto->isValid()) {
            throw new InvalidProfileImageException('Invalid image file: ' . $profilePhoto->getErrorMessage());
        }

        $filename = Uuid::random() . '.' . $profilePhoto->getClientOriginalExtension();
        $dateFolder = (new \DateTimeImmutable())->format('Ymd');
        $uploadDir = $this->getParameter('kernel.project_dir') . '/var/tmp/resource/' . $dateFolder . '/profile';
        $profilePhoto->move($uploadDir, $filename);

        $command = new UpdateUserProfilePhotoCommand(
            $sessionId,
            $uploadDir,
            $filename
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();

    }
}
