<?php declare(strict_types=1);

namespace App\Apps\Web\User\UpdateProfilePhoto;

use App\Contexts\Shared\Domain\ValueObject\Uuid;
use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\User\Application\UpdateProfilePhoto\UpdateUserProfilePhotoCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserProfilePhotoController extends ApiController
{

    public function __invoke(Request $request, string $sessionId): Response
    {
        /** @var UploadedFile $profilePhoto */
        $profilePhoto = $request->files->get('image');

        $filename = Uuid::random() . '.' . $profilePhoto->getClientOriginalExtension();
        $tempFolder = '/var/tmp/resource/'.(new \DateTimeImmutable())->format('Ymd').'/profile';
        $uploadDir = $this->getParameter('kernel.project_dir') . $tempFolder;
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