<?php declare(strict_types=1);

namespace App\Apps\Web\Post\Create;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\Create\CreatePostCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePostController extends ApiController
{
    public function __invoke(Request $request, string $sessionId): Response
    {
        $data = $request->request->all();

        /** @var UploadedFile $postImage */
        $postImage = $request->files->get('image');

        // Crear un nombre de archivo único
        $fileName = uniqid() . '.' . $postImage->getClientOriginalExtension();
        $uploads_dir = $this->getParameter('kernel.project_dir') . '/var/tmp';

        $postImage->move($uploads_dir, $fileName);

        $command = new CreatePostCommand(
            $data['id'],
            $data['body'],
            $uploads_dir . '/' . $fileName,
            $sessionId
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse();
    }
}