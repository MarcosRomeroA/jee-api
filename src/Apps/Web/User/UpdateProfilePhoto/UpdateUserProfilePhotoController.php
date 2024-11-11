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

    public function __invoke(Request $request, string $id): Response
    {
        /** @var UploadedFile $profilePhoto */
        $profilePhoto = $request->files->get('image');

        $fileName = Uuid::random() . '.' . $profilePhoto->getClientOriginalExtension();
        $uploads_dir = $this->getParameter('kernel.project_dir') . '/var/tmp';

        $profilePhoto->move($uploads_dir, $fileName);

        $command = new UpdateUserProfilePhotoCommand(
            $id,
            $uploads_dir . '/' . $fileName
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();

    }
}