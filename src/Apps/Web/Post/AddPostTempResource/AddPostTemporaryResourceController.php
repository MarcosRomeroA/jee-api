<?php

declare(strict_types=1);

namespace App\Apps\Web\Post\AddPostTempResource;

use App\Contexts\Shared\Infrastructure\Symfony\ApiController;
use App\Contexts\Web\Post\Application\AddPostTempResource\AddPostTempResourceCommand;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddPostTemporaryResourceController extends ApiController
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, string $id, string $sessionId): Response
    {
        /** @var UploadedFile $resource */
        $data = $request->request->all();
        $resource = $request->files->get('resource');

        $command = new AddPostTempResourceCommand(
            $data['id'],
            $id,
            $data['type'],
            $resource,
            $this->getParameter('kernel.project_dir')
        );

        $this->commandBus->dispatch($command);

        return $this->successEmptyResponse(Response::HTTP_ACCEPTED);
    }
}
